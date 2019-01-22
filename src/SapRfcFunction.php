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
            return $this->function->invoke($this->params);
        } catch (\Exception $exception) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
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
}
