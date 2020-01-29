<?php
/**
 * Either run tests using this mock of the sapnwrfc class or run the tests with the
 * actual module and an actual SAP system.
 */
if (extension_loaded('sapnwrfc')) {
    throw new \RuntimeException('PHP module sapnwrfc is loaded. Cannot run tests using mockups.');
}

/**
 * Class sapnwrfc
 *
 * Piers Hardings sapnwrfc class managing connections.
 *
 * @author  Gregor J.
 * @license MIT
 */
class sapnwrfc
{
    /**
     * Init connection using configuration array.
     * @param array $config
     */
    public function __construct($config)
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('sapnwrfc::' . __FUNCTION__);
        $func($config);
    }

    /**
     * Ping a connection.
     * @return bool
     */
    public function ping()
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('sapnwrfc::' . __FUNCTION__);
        return $func();
    }

    /**
     * Get SAP remote function by name and return sapnwrfc_function instance.
     * @param string $name
     * @return \sapnwrfc_function
     */
    public function function_lookup($name)
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('sapnwrfc::' . __FUNCTION__);
        return $func($name);
    }

    /**
     * Close a connection.
     */
    public function close()
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('sapnwrfc::' . __FUNCTION__);
        $func();
    }
}

class sapnwrfc_function
{
    /**
     * sapnwrfc function constructor.
     * @param string $name function name
     */
    public function __construct($name)
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('sapnwrfc_function::' . __FUNCTION__);
        $func($name);
    }

    /**
     * Invoke SAP remote function call.
     * @param array $params
     * @return array
     */
    public function invoke($params)
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('sapnwrfc_function::' . __FUNCTION__);
        return $func($params);
    }
}
