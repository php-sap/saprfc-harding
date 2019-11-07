<?php
/**
 * File tests/SapRfcFunctionTest.php
 *
 * Test function class.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractFunctionTestCase;

/**
 * Class tests\phpsap\saprfc\SapRfcFunctionTest
 *
 * Test function class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcFunctionTest extends AbstractFunctionTestCase
{
    /**
     * @var bool connection flag
     */
    private $connection = false;

    /**
     * @var string function name
     */
    private $function;

    /**
     * Implement methods of phpsap\IntegrationTests\AbstractTestCase
     */
    use SapRfcTestCaseTrait;

    /**
     * Mock the SAP RFC module for a successful SAP remote function call.
     */
    protected function mockSuccessfulFunctionCall()
    {
        $this->connection = false;
        $this->function = null;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[strtoupper($key)] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if ($config !== $expectedConfig) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('sapnwrfc::close', function () use ($self) {
            //calling sapnwrfc::close twice has to fail
            if ($self->connection !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $self->connection = false;
        });
        static::mock('sapnwrfc_function::__construct', function ($name) use ($self) {
            if ($name !== 'RFC_PING') {
                throw new \Exception('expected RFC_PING as mock function name!');
            }
            $self->function = $name;
        });
        static::mock('sapnwrfc::function_lookup', function ($name) use ($self) {
            if ($self->connection !== true) {
                throw new \Exception('mock connection not open!');
            }
            return new \sapnwrfc_function($name);
        });
        static::mock('sapnwrfc_function::invoke', function ($params) use ($self) {
            if ($self->function !== 'RFC_PING') {
                throw new \Exception('function not correctly initialized');
            }
            return [];
        });
    }

    /**
     * Mock the SAP RFC module for an unknown function call exception.
     */
    protected function mockUnknownFunctionException()
    {
        $this->connection = false;
        $this->function = null;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[strtoupper($key)] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if ($config !== $expectedConfig) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('sapnwrfc::close', function () use ($self) {
            //calling sapnwrfc::close twice has to fail
            if ($self->connection !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $self->connection = false;
        });
        static::mock('sapnwrfc::function_lookup', function ($name) {
            throw new \Exception(sprintf('function %s not found', $name));
        });
    }

    /**
     * Mock the SAP RFC module for a successful SAP remote function call with
     * parameters and results.
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
    {
        $this->connection = false;
        $this->function = null;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[strtoupper($key)] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if ($config !== $expectedConfig) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('sapnwrfc::close', function () use ($self) {
            //calling sapnwrfc::close twice has to fail
            if ($self->connection !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $self->connection = false;
        });
        static::mock('sapnwrfc_function::__construct', function ($name) use ($self) {
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new \Exception('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            $self->function = $name;
        });
        static::mock('sapnwrfc::function_lookup', function ($name) use ($self) {
            if ($self->connection !== true) {
                throw new \Exception('mock connection not open!');
            }
            return new \sapnwrfc_function($name);
        });
        static::mock('sapnwrfc_function::invoke', function ($params) use ($self) {
            if ($self->function !== 'RFC_WALK_THRU_TEST') {
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
     * Mock the SAP RFC module for a failed SAP remote function call with parameters.
     */
    protected function mockFailedRemoteFunctionCallWithParameters()
    {
        $this->connection = false;
        $this->function = null;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[strtoupper($key)] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if ($config !== $expectedConfig) {
                throw new \Exception('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('sapnwrfc::close', function () use ($self) {
            //calling sapnwrfc::close twice has to fail
            if ($self->connection !== true) {
                throw new \Exception('mock connection already closed!');
            }
            $self->connection = false;
        });
        static::mock('sapnwrfc_function::__construct', function ($name) use ($self) {
            if ($name !== 'RFC_READ_TABLE') {
                throw new \Exception('expected RFC_READ_TABLE as mock function name!');
            }
            $self->function = $name;
        });
        static::mock('sapnwrfc::function_lookup', function ($name) use ($self) {
            if ($self->connection !== true) {
                throw new \Exception('mock connection not open!');
            }
            return new \sapnwrfc_function($name);
        });
        static::mock('sapnwrfc_function::invoke', function ($params) use ($self) {
            throw new \Exception('mock function call exception!');
        });
    }
}
