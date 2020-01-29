<?php

namespace phpsap\saprfc\Traits;

use phpsap\classes\Api\Element;
use phpsap\classes\Api\Table;
use phpsap\exceptions\FunctionCallException;

/**
 * Trait ParamTrait
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait ParamTrait
{
    /**
     * Generate a function call parameter array from a list of known input values
     * and the previously set parameters.
     * @param \phpsap\classes\Api\Value[] $inputs API input values.
     * @param array                       $params Parameters
     * @return array
     * @throws \phpsap\exceptions\FunctionCallException
     */
    private function getInputParams($inputs, $params)
    {
        $result = [];
        foreach ($inputs as $input) {
            $key = $input->getName();
            if (array_key_exists($key, $params)) {
                $result[$key] = $params[$key];
            } elseif (!$input->isOptional()) {
                throw new FunctionCallException(sprintf(
                    'Missing parameter \'%s\' for function call \'%s\'!',
                    $key,
                    $this->getName()
                ));
            }
        }
        return $result;
    }

    /**
     * Generate a function call parameter array from a list of known tables and the
     * previously set parameters.
     * @param \phpsap\classes\Api\Table[] $tables
     * @param array                       $params
     * @return array
     */
    private function getTableParams($tables, $params)
    {
        $result = [];
        foreach ($tables as $table) {
            $key = $table->getName();
            if (
                array_key_exists($key, $params)
                && is_array($params[$key])
                && count($params[$key]) > 0
            ) {
                $result[$key] = $params[$key];
            }
        }
        return $result;
    }

    /**
     * @param \phpsap\classes\Api\Value[] $outputs
     * @param array                       $result
     * @return array
     */
    private function castOutputValues($outputs, $result)
    {
        $return = [];
        foreach ($outputs as $output) {
            $key = $output->getName();
            $value = $output->cast($result[$key]);
            $type = $output->getType();
            if (
                $type === Element::TYPE_STRING
                || $type === Table::TYPE_ARRAY
            ) {
                $value = $this->rtrimStrings($value);
            }
            $return[$key] = $value;
        }
        return $return;
    }

    /**
     * Trim all trailing spaces, newlines and null-bytes from strings.
     * @param mixed $return
     * @return mixed
     */
    private function rtrimStrings($return)
    {
        if (is_string($return)) {
            return rtrim($return);
        }
        if (is_array($return)) {
            foreach ($return as $key => $value) {
                $return[$key] = $this->rtrimStrings($value);
            }
        }
        return $return;
    }
}
