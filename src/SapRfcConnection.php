<?php
/**
 * File src/SapRfcConnection.php
 *
 * PHP/SAP connections using Piers Hardings sapnwrfc module.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConnection;
use phpsap\exceptions\ConnectionFailedException;

/**
 * Class phpsap\saprfc\SapRfcConnection
 *
 * PHP/SAP connection class abstracting connection related functions using Piers
 * Hardings sapnwrfc module.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnection extends AbstractConnection
{
    /**
     * Establish connection.
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function connect()
    {
        if ($this->isConnected()) {
            $this->close();
        }
        try {
            $this->connection = new \sapnwrfc($this->config);
        } catch (\Exception $exception) {
            $this->connection = null;
            throw new ConnectionFailedException(sprintf(
                'Connection %s creation failed: %s',
                $this->getId(),
                $exception->getMessage()
            ), 0, $exception);
        }
    }

    /**
     * Send a ping request via an established connection to verify that the
     * connection works.
     * @return boolean success?
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function ping()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        return ($this->connection->ping() === true);
    }

    /**
     * Closes the connection instance of the underlying PHP module.
     */
    public function close()
    {
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \phpsap\classes\AbstractFunction
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function createFunctionInstance($name)
    {
        return new SapRfcFunction($this->getConnection(), $name);
    }
}
