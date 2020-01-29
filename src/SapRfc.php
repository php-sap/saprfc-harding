<?php

namespace phpsap\saprfc;

use Exception;
use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\RemoteApi;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\SapLogicException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\saprfc\Traits\ApiTrait;
use phpsap\saprfc\Traits\ConfigTrait;
use phpsap\saprfc\Traits\ParamTrait;
use sapnwrfc;

/**
 * Class SapRfc
 *
 * DESCRIPTION
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfc extends AbstractFunction
{
    use ApiTrait;

    use ConfigTrait;

    use ParamTrait;

    /**
     * @var \sapnwrfc
     */
    private $connection;

    /**
     * @var \sapnwrfc_function
     */
    private $function;

    /**
     * Cleanup method.
     */
    public function __destruct()
    {
        if ($this->function !== null) {
            $this->function = null;
        }
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Create a remote function call resource.
     * @return \sapnwrfc_function
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\IncompleteConfigException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function getFunction()
    {
        if ($this->function === null) {
            /**
             * Create a new function resource.
             */
            try {
                $this->function = $this
                    ->getConnection()
                    ->function_lookup($this->getName());
            } catch (Exception $exception) {
                /**
                 * sapnwrfc::function_lookup() only throws \Exception. Therefore we
                 * distinguish between the exceptions thrown by PHP/SAP and the
                 * exceptions thrown by sapnwrfc::function_lookup().
                 */
                if (
                    $exception instanceof ConnectionFailedException
                    || $exception instanceof IncompleteConfigException
                ) {
                    throw $exception;
                }
                throw new UnknownFunctionException(sprintf(
                    'Unknown function %s: %s',
                    $this->getName(),
                    $exception->getMessage()
                ), 0, $exception);
            }
        }
        return $this->function;
    }

    /**
     * Open a connection in case it hasn't been done yet and return the
     * connection resource.
     * @return \sapnwrfc
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    protected function getConnection()
    {
        if ($this->connection === null) {
            /**
             * In case the is no configuration, throw an exception.
             */
            if (($config = $this->getConfiguration()) === null) {
                throw new IncompleteConfigException(
                    'Configuration is missing!'
                );
            }
            /**
             * Catch generic IIncompleteConfigException interface and throw the
             * actual exception class of this repository.
             */
            try {
                $moduleConfig = $this->getModuleConfig($config);
            } catch (IIncompleteConfigException $exception) {
                throw new IncompleteConfigException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                );
            }
            /**
             * Create a new connection resource.
             */
            try {
                $this->connection = new sapnwrfc($moduleConfig);
            } catch (Exception $exception) {
                $this->connection = null;
                throw new ConnectionFailedException(sprintf(
                    'Connection creation failed: %s',
                    $exception->getMessage()
                ), 0, $exception);
            }
        }
        return $this->connection;
    }

    /**
     * @inheritDoc
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function extractApi()
    {
        /**
         * InvalidArgumentException is never thrown, because no parameter is given.
         */
        $api = new RemoteApi();
        foreach ($this->saprfcFunctionInterface() as $name => $element) {
            try {
                $api->add($this->createApiValue(
                    strtoupper($name),
                    $this->mapType($element['type']),
                    $this->mapDirection($element['direction']),
                    $element['optional']
                ));
            } catch (SapLogicException $exception) {
                /**
                 * InvalidArgumentException is a child of SapLogicException and will
                 * be caught too.
                 */
                throw new ConnectionFailedException(
                    'The API behaved unexpectedly: ' . $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                );
            }
        }
        return $api;
    }

    /**
     * Extract the remote function API from the function object and remove unwanted variables.
     * @return array
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\IncompleteConfigException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function saprfcFunctionInterface()
    {
        $result = get_object_vars($this->getFunction());
        unset($result['name']);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function invoke()
    {
        /**
         * Merge value and table parameters into one parameter array.
         */
        $params = array_merge(
            $this->getInputParams(
                $this->getApi()->getInputValues(),
                $this->getParams()
            ),
            $this->getTableParams(
                $this->getApi()->getTables(),
                $this->getParams()
            )
        );
        /**
         * Invoke SAP remote function call.
         */
        try {
            $result = $this
                ->getFunction()
                ->invoke($params);
        } catch (Exception $exception) {
            /**
             * sapnwrfc_function::invoke() only throws \Exception. Therefore we
             * distinguish between the exceptions thrown by PHP/SAP and the
             * exceptions thrown by sapnwrfc_function::invoke().
             */
            if (
                $exception instanceof ConnectionFailedException
                || $exception instanceof IncompleteConfigException
                || $exception instanceof UnknownFunctionException
            ) {
                throw $exception;
            }
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
        /**
         * Typecast the return values.
         */
        return $this->castOutputValues(array_merge(
            $this->getApi()->getOutputValues(),
            $this->getApi()->getTables()
        ), $result);
    }
}
