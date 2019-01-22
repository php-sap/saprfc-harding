<?php
/**
 * File src/SapRfcFunction.php
 *
 * PHP/SAP remote function calls using Piers Hardings sapnwrfc module.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractFunction;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\UnknownFunctionException;

/**
 * Class phpsap\saprfc\SapRfcFunction
 *
 * PHP/SAP remote function class abstracting remote function call related functions
 * using Piers Hardings sapnwrfc module.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcFunction extends AbstractFunction
{
    /**
     * @var \sapnwrfc
     */
    protected $connection;

    /**
     * @var \sapnwrfc_function
     */
    protected $function;

    /**
     * Clear remote function call instance.
     */
    public function __destruct()
    {
        if ($this->function !== null) {
            $this->function = null;
        }
    }

    /**
     * Execute the prepared function call.
     * @return array
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\FunctionCallException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function execute()
    {
        try {
            $result = $this->function->invoke($this->params);
        } catch (\Exception $exception) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
        return $this->trimStrings($result);
    }

    /**
     * Lookup SAP remote function and return an module class instance of it.
     * @return \sapnwrfc_function
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function getFunction()
    {
        try {
            return $this->connection->function_lookup($this->getName());
        } catch (\Exception $exception) {
            throw new UnknownFunctionException(sprintf(
                'Unknown function %s: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
    }

    /**
     * Trim the strings of a given data structure.
     * @param mixed $return
     * @return mixed
     */
    protected function trimStrings($return)
    {
        if (is_string($return)) {
            /**
             * Do not trim strings containing line breaks.
             */
            if (strtr($return, "\n") || strtr($return, "\r\n")) {
                return $return;
            }
            return rtrim($return);
        }
        if (is_array($return)) {
            foreach ($return as $key => $value) {
                $return[$key] = $this->trimStrings($value);
            }
        }
        return $return;
    }
}
