<?php
/**
 * File tests/SapRfcConfigATest.php
 *
 * Test config type A.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConfigATestCase;
use phpsap\saprfc\SapRfcConfigA;

/**
 * Class tests\phpsap\saprfc\SapRfcConfigATest
 *
 * Test config type A.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigATest extends AbstractConfigATestCase
{
    /**
     * Return a new instance of a PHP/SAP config type A.
     * @param array|string|null $config PHP/SAP config JSON/array. Default: null
     * @return \phpsap\saprfc\SapRfcConfigA
     */
    public function newConfigA($config = null)
    {
        return new SapRfcConfigA($config);
    }
}
