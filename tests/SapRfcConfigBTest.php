<?php
/**
 * File tests/SapRfcConfigBTest.php
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

    /**
     * Assert the actual module configuration variable.
     * @param mixed $configSaprfc
     * @param string $client
     * @param string $user
     * @param string $passwd
     * @param string $mshost
     * @param string $r3name
     * @param string $group
     * @param string $lang
     * @param int $trace
     */
    public function assertValidModuleConfig(
        $configSaprfc,
        $client,
        $user,
        $passwd,
        $mshost,
        $r3name,
        $group,
        $lang,
        $trace
    ) {
        static::assertInternalType('array', $configSaprfc);
        static::assertArrayHasKey('CLIENT', $configSaprfc);
        static::assertSame($client, $configSaprfc['CLIENT']);
        static::assertArrayHasKey('USER', $configSaprfc);
        static::assertSame($user, $configSaprfc['USER']);
        static::assertArrayHasKey('PASSWD', $configSaprfc);
        static::assertSame($passwd, $configSaprfc['PASSWD']);
        static::assertArrayHasKey('MSHOST', $configSaprfc);
        static::assertSame($mshost, $configSaprfc['MSHOST']);
        static::assertArrayHasKey('R3NAME', $configSaprfc);
        static::assertSame($r3name, $configSaprfc['R3NAME']);
        static::assertArrayHasKey('GROUP', $configSaprfc);
        static::assertSame($group, $configSaprfc['GROUP']);
        static::assertArrayHasKey('LANG', $configSaprfc);
        static::assertSame($lang, $configSaprfc['LANG']);
        static::assertArrayHasKey('TRACE', $configSaprfc);
        static::assertSame($trace, $configSaprfc['TRACE']);
    }
}
