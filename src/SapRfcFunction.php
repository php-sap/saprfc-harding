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
     * PHP module connection object.
     * @var \sapnwrfc
     */
    protected $connection;

    /**
     * PHP module function object.
     * @var \sapnwrfc_function
     */
    protected $function;

    /**
     * Clear remote function call instance.
     */
    public function __destruct()
    {
        $this->connection = null;
        $this->function = null;
    }

    /**
     * Execute the prepared function call.
     * @return array
     * @throws \phpsap\exceptions\FunctionCallException
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
    private function trimStrings($return)
    {
        if (is_string($return)) {
            return $this->rTrim($return);
        }
        if (is_array($return)) {
            foreach ($return as $key => $value) {
                $return[$key] = $this->trimStrings($value);
            }
        }
        return $return;
    }

    /**
     * Trim a string.
     * @param string $string
     * @return string
     */
    private function rTrim($string)
    {
        /**
         * Do not trim strings containing non-printable characters.
         */
        if (!ctype_print($string)) {
            return $string;
        }
        return rtrim($string);
    }
}
