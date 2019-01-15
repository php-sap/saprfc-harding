<?php
/**
 * File src/SapRfcConfigBTest.php
 *
 * Test config type B.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConfigBTestCase;
use phpsap\saprfc\SapRfcConfigB;

/**
 * Class tests\phpsap\saprfc\SapRfcConfigBTest
 *
 * Test config type B.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigBTest extends AbstractConfigBTestCase
{
    /**
     * Return a new instance of a PHP/SAP config type B.
     * @param array|string|null $config PHP/SAP config JSON/array. Default: null
     * @return \phpsap\saprfc\SapRfcConfigB
     */
    public function newConfigB($config = null)
    {
        return new SapRfcConfigB($config);
    }
}
