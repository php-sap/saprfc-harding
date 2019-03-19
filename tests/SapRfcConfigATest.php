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

    /**
     * Assert the actual module configuration variable.
     * @param array $configSaprfc
     * @param string $ashost
     * @param string $sysnr
     * @param string $client
     * @param string $user
     * @param string $passwd
     * @param string $gwhost
     * @param string $gwserv
     * @param string $lang
     * @param string $trace
     */
    public function assertValidModuleConfig(
        $configSaprfc,
        $ashost,
        $sysnr,
        $client,
        $user,
        $passwd,
        $gwhost,
        $gwserv,
        $lang,
        $trace
    ) {
        static::assertInternalType('array', $configSaprfc);
        static::assertArrayHasKey('ASHOST', $configSaprfc);
        static::assertSame($ashost, $configSaprfc['ASHOST']);
        static::assertArrayHasKey('SYSNR', $configSaprfc);
        static::assertSame($sysnr, $configSaprfc['SYSNR']);
        static::assertArrayHasKey('CLIENT', $configSaprfc);
        static::assertSame($client, $configSaprfc['CLIENT']);
        static::assertArrayHasKey('USER', $configSaprfc);
        static::assertSame($user, $configSaprfc['USER']);
        static::assertArrayHasKey('PASSWD', $configSaprfc);
        static::assertSame($passwd, $configSaprfc['PASSWD']);
        static::assertArrayHasKey('GWHOST', $configSaprfc);
        static::assertSame($gwhost, $configSaprfc['GWHOST']);
        static::assertArrayHasKey('GWSERV', $configSaprfc);
        static::assertSame($gwserv, $configSaprfc['GWSERV']);
        static::assertArrayHasKey('LANG', $configSaprfc);
        static::assertSame($lang, $configSaprfc['LANG']);
        static::assertArrayHasKey('TRACE', $configSaprfc);
        static::assertSame($trace > 0, $configSaprfc['TRACE']);
    }
}
