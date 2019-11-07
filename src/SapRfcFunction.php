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
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\classes\RemoteApi;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;

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
     * Set function call parameter.
     * @param string                           $name
     * @param array|string|float|int|bool|null $value
     * @return \phpsap\classes\AbstractFunction $this
     * @throws \InvalidArgumentException
     */
    public function setParam($name, $value)
    {
        if (!is_array($value)) {
            $value = (string) $value;
        }
        parent::setParam($name, $value);
        return $this;
    }

    /**
     * Execute the prepared function call.
     * @return array
     * @throws \phpsap\exceptions\FunctionCallException
     */
    public function invoke()
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
        //remove trailing null- and space-chars.
        $result = $this->trimStrings($result);
        //typecast result
        return $this->cast($result, array_merge(
            $this->getApi()->getOutputValues(),
            $this->getApi()->getTables()
        ));
    }

    /**
     * Typecast the results array.
     * @param array    $results
     * @param IValue[] $apiValues
     * @return array
     */
    private function cast(array $results, array $apiValues)
    {
        foreach ($apiValues as $apiValue) {
            $name = $apiValue->getName();
            if (array_key_exists($name, $results)) {
                $results[$name] = $apiValue->cast($results[$name]);
            }
        }
        return $results;
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

    /**
     * Extract the remote function API and return an API description class.
     * @return RemoteApi
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function extractApi()
    {
        $api = new RemoteApi();
        foreach ($this->saprfcFunctionInterface() as $name => $element) {
            $api->add($this->createApiValue(
                strtoupper($name),
                $this->mapType($element['type']),
                $this->mapDirection($element['direction']),
                $element['optional']
            ));
        }
        return $api;
    }

    /**
     * Extract the remote function API from the function object and remove unwanted variables.
     * @return array
     */
    public function saprfcFunctionInterface()
    {
        $result = get_object_vars($this->function);
        unset($result['name']);
        return $result;
    }

    /**
     * Create either Value, Struct or Table from a given remote function parameter or return value.
     * @param string $name The name of the parameter or return value.
     * @param string $type The type of the parameter or return value.
     * @param string $direction The direction indicating whether it's a parameter or return value.
     * @param bool $optional The flag, whether this parameter or return value is required.
     * @return Value|Struct|Table
     */
    private function createApiValue($name, $type, $direction, $optional)
    {
        if ($direction === IArray::DIRECTION_TABLE) {
            /**
             * The members array is empty because there is no information about it
             * from the sapnwrfc module class.
             * @todo Write to Piers Harding.
             */
            return new Table($name, $optional, []);
        }
        if ($type === IArray::TYPE_ARRAY) {
            /**
             * The members array is empty because there is no information about it
             * from the sapnwrfc module class.
             * @todo Write to Piers Harding.
             */
            return new Struct($name, $direction, $optional, []);
        }
        return new Value($type, $name, $direction, $optional);
    }

    /**
     * Convert SAP Netweaver RFC types into PHP/SAP types.
     * @param string $type The remote function parameter type.
     * @return string The PHP/SAP internal data type.
     * @throws \LogicException In case the given SAP Netweaver RFC type is missing in the mapping table.
     */
    private function mapType($type)
    {
        $mapping = [
            'RFCTYPE_DATE'      => IElement::TYPE_DATE,
            'RFCTYPE_TIME'      => IElement::TYPE_TIME,
            'RFCTYPE_INT'       => IElement::TYPE_INTEGER,
            'RFCTYPE_NUM'       => IElement::TYPE_INTEGER,
            'RFCTYPE_INT1'      => IElement::TYPE_INTEGER,
            'RFCTYPE_INT2'      => IElement::TYPE_INTEGER,
            'RFCTYPE_BCD'       => IElement::TYPE_FLOAT,
            'RFCTYPE_FLOAT'     => IElement::TYPE_FLOAT,
            'RFCTYPE_CHAR'      => IElement::TYPE_STRING,
            'RFCTYPE_STRING'    => IElement::TYPE_STRING,
            'RFCTYPE_BYTE'      => IElement::TYPE_HEXBIN,
            'RFCTYPE_XSTRING'   => IElement::TYPE_HEXBIN,
            'RFCTYPE_STRUCTURE' => IArray::TYPE_ARRAY,
            'RFCTYPE_TABLE'     => IArray::TYPE_ARRAY
        ];
        if (!array_key_exists($type, $mapping)) {
            throw new \LogicException(sprintf('Unknown SAP Netweaver RFC type \'%s\'!', $type));
        }
        return $mapping[$type];
    }

    /**
     * Convert SAP Netweaver RFC directions into PHP/SAP directions.
     * @param string $direction The remote function parameter direction.
     * @return string The PHP/SAP internal direction.
     * @throws \LogicException In case the given SAP Netweaver RFC direction is missing in the mapping table.
     */
    private function mapDirection($direction)
    {
        $mapping = [
            'RFC_EXPORT' => IValue::DIRECTION_OUTPUT,
            'RFC_IMPORT' => IValue::DIRECTION_INPUT,
            'RFC_TABLES' => IArray::DIRECTION_TABLE
        ];
        if (!array_key_exists($direction, $mapping)) {
            throw new \LogicException(sprintf('Unknown SAP Netweaver RFC direction \'%s\'!', $direction));
        }
        return $mapping[$direction];
    }
}
