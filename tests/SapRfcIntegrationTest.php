<?php

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractSapRfcTestCase;
use tests\phpsap\saprfc\Traits\TestCaseTrait;

/**
 * Class tests\phpsap\saprfc\SapRfcIntegrationTest
 *
 * Implement methods of the integration tests to mock SAP remote function
 * calls without an actual SAP system for testing.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
class SapRfcIntegrationTest extends AbstractSapRfcTestCase
{
    use TestCaseTrait;

    /**
     * @var array raw API of RFC walk through test
     */
    public static $rfcWalkThruTestApi = [
        'name' => 'RFC_WALK_THRU_TEST',
        'TEST_OUT' => [
            'type' => 'RFCTYPE_STRUCTURE',
            'direction' => 'RFC_EXPORT',
            'description' => 'ZZ8ruKPn6j',
            'optional' => false,
            'defaultValue' => '',
        ],
        'TEST_IN' => [
            'type' => 'RFCTYPE_STRUCTURE',
            'direction' => 'RFC_IMPORT',
            'description' => 'p1f4ghivbE',
            'optional' => false,
            'defaultValue' => '',
        ],
        'DESTINATIONS' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Xv7XloU3Jg',
            'optional' => false,
            'defaultValue' => '',
        ],
        'LOG' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'GriBlkJUOO',
            'optional' => false,
            'defaultValue' => '',
        ]
    ];

    /**
     * @var array raw API of RFC read table
     */
    public static $rfcReadTableApi = [
        'name' => 'RFC_READ_TABLE',
        'DELIMITER' => [
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => 'Zeichen für Markierung von Feldgrenzen in DATA',
            'optional' => true,
            'defaultValue' => 'SPACE',
        ],
        'NO_DATA' => [
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => 'falls <> SPACE, wird nur FIELDS gefüllt',
            'optional' => true,
            'defaultValue' => 'SPACE',
        ],
        'QUERY_TABLE' => [
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => 'Tabelle, aus der gelesen wird',
            'optional' => false,
            'defaultValue' => '',
        ],
        'ROWCOUNT' => [
            'type' => 'RFCTYPE_INT',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'optional' => true,
            'defaultValue' => '0',
        ],
        'ROWSKIPS' => [
            'type' => 'RFCTYPE_INT',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'optional' => true,
            'defaultValue' => '0',
        ],
        'DATA' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'gelesene Daten (out)',
            'optional' => false,
            'defaultValue' => '',
        ],
        'FIELDS' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Namen (in) und Struktur (out) gelesener Felder',
            'optional' => false,
            'defaultValue' => '',
        ],
        'OPTIONS' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Selektionsangaben, "WHERE-Klauseln" (in)',
            'optional' => false,
            'defaultValue' => '',
        ]
    ];

    /**
     * Clean up after tests.
     */
    public function tearDown()
    {
        parent::tearDown();
        $devRfcTrc = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dev_rfc.trc';
        if (file_exists($devRfcTrc)) {
            unlink($devRfcTrc);
        }
    }

    /**
     * @inheritDoc
     */
    protected function mockConnectionFailed()
    {
        static::mock('sapnwrfc::__construct', static function ($config) {
            unset($config);
            throw new \Exception('mock failed connection');
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockSuccessfulRfcPing()
    {
        $flags = new \stdClass();
        $flags->conn = false;
        $flags->func = null;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('sapnwrfc::__construct', static function ($config) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ASHOST', $config)
                || !array_key_exists('SYSNR', $config)
                || !array_key_exists('CLIENT', $config)
                || !array_key_exists('USER', $config)
                || !array_key_exists('PASSWD', $config)
                || $config['ASHOST'] !== $expectedConfig->getAshost()
                || $config['SYSNR'] !== $expectedConfig->getSysnr()
                || $config['CLIENT'] !== $expectedConfig->getClient()
                || $config['USER'] !== $expectedConfig->getUser()
                || $config['PASSWD'] !== $expectedConfig->getPasswd()
            ) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('sapnwrfc::close', static function () use ($flags) {
            //calling sapnwrfc::close twice has to fail
            if ($flags->conn !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $flags->conn = false;
        });
        static::mock('sapnwrfc::function_lookup', static function ($name) {
            return new \sapnwrfc_function($name);
        });
        static::mock('sapnwrfc_function::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($name !== 'RFC_PING') {
                throw new \Exception('expected RFC_PING as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('sapnwrfc_function::invoke', static function ($params) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($flags->func !== 'RFC_PING') {
                throw new \Exception('mock function not correctly constructed!');
            }
            if (!empty($params)) {
                throw new \Exception('mock RFC_PING received parameters! ' . json_encode($params));
            }
            return [];
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockUnknownFunctionException()
    {
        $connFlag = false;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('sapnwrfc::__construct', static function ($config) use (&$connFlag, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ASHOST', $config)
                || !array_key_exists('SYSNR', $config)
                || !array_key_exists('CLIENT', $config)
                || !array_key_exists('USER', $config)
                || !array_key_exists('PASSWD', $config)
                || $config['ASHOST'] !== $expectedConfig->getAshost()
                || $config['SYSNR'] !== $expectedConfig->getSysnr()
                || $config['CLIENT'] !== $expectedConfig->getClient()
                || $config['USER'] !== $expectedConfig->getUser()
                || $config['PASSWD'] !== $expectedConfig->getPasswd()
            ) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $connFlag = true;
        });
        static::mock('sapnwrfc::close', static function () use (&$connFlag) {
            //calling sapnwrfc::close twice has to fail
            if ($connFlag !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $connFlag = false;
        });
        static::mock('sapnwrfc::function_lookup', static function ($name) {
            throw new \Exception(sprintf('function %s not found', $name));
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
    {
        //Use an object for connection flag and function name.
        $flags = new \stdClass();
        $flags->conn = false;
        $flags->func = null;
        $flags->api = static::$rfcWalkThruTestApi;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('sapnwrfc::__construct', static function ($config) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ASHOST', $config)
                || !array_key_exists('SYSNR', $config)
                || !array_key_exists('CLIENT', $config)
                || !array_key_exists('USER', $config)
                || !array_key_exists('PASSWD', $config)
                || $config['ASHOST'] !== $expectedConfig->getAshost()
                || $config['SYSNR'] !== $expectedConfig->getSysnr()
                || $config['CLIENT'] !== $expectedConfig->getClient()
                || $config['USER'] !== $expectedConfig->getUser()
                || $config['PASSWD'] !== $expectedConfig->getPasswd()
            ) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('sapnwrfc::close', static function () use ($flags) {
            //calling sapnwrfc::close twice has to fail
            if ($flags->conn !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $flags->conn = false;
        });
        static::mock('sapnwrfc_function::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new \Exception('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('sapnwrfc::function_lookup', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new \Exception('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            $func = new \sapnwrfc_function($name);
            //Assigning all the API values that are later gathered by get_object_vars().
            foreach ($flags->api as $key => $value) {
                $func->$key = $value;
            }
            return $func;
        });
        static::mock('sapnwrfc_function::invoke', static function ($params) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($flags->func !== 'RFC_WALK_THRU_TEST') {
                throw new \Exception('function not correctly initialized!');
            }
            return [
                'TEST_OUT' => [
                    'RFCFLOAT' => 70.109999999999999,
                    'RFCCHAR1' => 'A',
                    'RFCINT2' => 4095,
                    'RFCINT1' => 163,
                    'RFCCHAR4' => 'QqMh',
                    'RFCINT4' => 416639,
                    'RFCHEX3' => '53' . "\0" . '',
                    'RFCCHAR2' => 'XC',
                    'RFCTIME' => '102030',
                    'RFCDATE' => '20191030',
                    'RFCDATA1' => 'qKWjmNfad32rfS9Z                                  ',
                    'RFCDATA2' => 'xi82ph2zJ8BCVtlR                                  '
                ],
                'DESTINATIONS' => [],
                'LOG' => [
                    [
                        'RFCDEST' => 'AOP3                            ',
                        'RFCWHOAMI' => 'pzjti000                        ',
                        'RFCLOG' => 'FAP-RytEHBsRYKX AOP3 eumqvMJD ZLqovj.                                 '
                    ]
                ]
            ];
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockFailedRemoteFunctionCallWithParameters()
    {
        //Use an object for connection flag and function name.
        $flags = new \stdClass();
        $flags->conn = false;
        $flags->func = null;
        $flags->api = static::$rfcReadTableApi;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('sapnwrfc::__construct', static function ($config) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ASHOST', $config)
                || !array_key_exists('SYSNR', $config)
                || !array_key_exists('CLIENT', $config)
                || !array_key_exists('USER', $config)
                || !array_key_exists('PASSWD', $config)
                || $config['ASHOST'] !== $expectedConfig->getAshost()
                || $config['SYSNR'] !== $expectedConfig->getSysnr()
                || $config['CLIENT'] !== $expectedConfig->getClient()
                || $config['USER'] !== $expectedConfig->getUser()
                || $config['PASSWD'] !== $expectedConfig->getPasswd()
            ) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('sapnwrfc::close', static function () use ($flags) {
            //calling sapnwrfc::close twice has to fail
            if ($flags->conn !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $flags->conn = false;
        });
        static::mock('sapnwrfc_function::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($name !== 'RFC_READ_TABLE') {
                throw new \Exception('expected RFC_READ_TABLE as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('sapnwrfc::function_lookup', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \Exception('mock connection not open!');
            }
            if ($name !== 'RFC_READ_TABLE') {
                throw new \Exception('expected RFC_READ_TABLE as mock function name!');
            }
            $func = new \sapnwrfc_function($name);
            //Assigning all the API values that are later gathered by get_object_vars().
            foreach ($flags->api as $key => $value) {
                $func->$key = $value;
            }
            return $func;
        });
        static::mock('sapnwrfc_function::invoke', static function ($params) use ($flags) {
            throw new \Exception('mock function call exception!');
        });
    }
}
