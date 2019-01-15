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
            if ($name !== 'Z_MC_GET_DATE_TIME') {
                throw new \Exception('expected Z_MC_GET_DATE_TIME as mock function name!');
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
            if ($self->function !== 'Z_MC_GET_DATE_TIME') {
                throw new \Exception('function not correctly initialized!');
            }
            if ($params !== ['IV_DATE' => '20181119']) {
                throw new \Exception('unexpected parameters array!');
            }
            return [
                'EV_FRIDAY'         => '20181123   ',
                'EV_FRIDAY_LAST'    => '20181116   ',
                'EV_FRIDAY_NEXT'    => '20181130   ',
                'EV_FRITXT'         => 'Freitag    ',
                'EV_MONDAY'         => '20181119   ',
                'EV_MONDAY_LAST'    => '20181112   ',
                'EV_MONDAY_NEXT'    => '20181126   ',
                'EV_MONTH'          => '11         ',
                'EV_MONTH_LAST_DAY' => '20181130   ',
                'EV_MONTXT'         => 'Montag     ',
                'EV_TIMESTAMP'      => 'NOVALUE    ',
                'EV_WEEK'           => '201847     ',
                'EV_WEEK_LAST'      => '201846     ',
                'EV_WEEK_NEXT'      => '201848     ',
                'EV_YEAR'           => '2018       '
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
            if ($name !== 'Z_MC_GET_DATE_TIME') {
                throw new \Exception('expected Z_MC_GET_DATE_TIME as mock function name!');
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
