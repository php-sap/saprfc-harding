<?php
/**
 * File tests/SapRfcConnectionTest.php
 *
 * Test connection class.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConnectionTestCase;

/**
 * Class tests\phpsap\saprfc\SapRfcConnectionTest
 *
 * Test connection class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnectionTest extends AbstractConnectionTestCase
{
    /**
     * @var bool
     */
    protected $connection = false;

    /**
     * Implement methods of phpsap\IntegrationTests\AbstractTestCase
     */
    use SapRfcTestCaseTrait;

    /**
     * Mock the SAP RFC module for a successful connection attempt.
     */
    protected function mockSuccessfulConnect()
    {
        $this->connection = false;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[(strtoupper($key))] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if (!is_array($config) && $config !== $expectedConfig) {
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
    }

    /**
     * Mock the SAP RFC module for a failed connection attempt.
     */
    protected function mockFailedConnect()
    {
        $this->connection = false;
        static::mock('sapnwrfc::__construct', function ($config) {
            throw new \Exception('mock failed connection');
        });
    }

    /**
     * Mock the SAP RFC module for a successful attempt to ping a connection.
     */
    protected function mockSuccessfulPing()
    {
        $this->connection = false;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[(strtoupper($key))] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if (!is_array($config) && $config !== $expectedConfig) {
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
        static::mock('sapnwrfc::ping', function () use ($self) {
            //return true only in case a connection has been established.
            return $self->connection;
        });
    }

    /**
     * Mock the SAP RFC module for a failed attempt to ping a connection.
     */
    protected function mockFailedPing()
    {
        $this->connection = false;
        $self = $this;
        //compile expected config
        $expectedConfig = [];
        foreach ($this->getSampleSapConfig() as $key => $value) {
            $expectedConfig[(strtoupper($key))] = $value;
        }
        static::mock('sapnwrfc::__construct', function ($config) use ($self, $expectedConfig) {
            if (!is_array($config) && $config !== $expectedConfig) {
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
        static::mock('sapnwrfc::ping', function () {
            return false;
        });
    }
}
