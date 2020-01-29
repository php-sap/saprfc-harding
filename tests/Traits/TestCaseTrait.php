<?php

namespace tests\phpsap\saprfc\Traits;

use phpsap\saprfc\SapRfc;

/**
 * Trait TestCaseTrait
 *
 * Collect methods common to all test cases extending the integration tests.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
trait TestCaseTrait
{
    /**
     * Return the name of the class, used for testing.
     * @return string
     */
    public static function getClassName()
    {
        return SapRfc::class;
    }

    /**
     * Get the name of the PHP module.
     * @return string
     */
    public static function getModuleName()
    {
        return 'sapnwrfc';
    }

    /**
     * Get the path to the PHP/SAP configuration file.
     * @return string
     */
    public static function getSapConfigFile()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sap.json';
    }

    /**
     * Get the path to the filename containing the SAP RFC module mockups.
     * @return string
     */
    public static function getModuleTemplateFile()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'sapnwrfc.php';
    }

    /**
     * Get an array of valid SAP RFC module function or class method names.
     * @return array
     */
    public static function getValidModuleFunctions()
    {
        return [
            'sapnwrfc::__construct',
            'sapnwrfc::close',
            'sapnwrfc::ping',
            'sapnwrfc::function_lookup',
            'sapnwrfc_function::__construct',
            'sapnwrfc_function::invoke'
        ];
    }

    /**
     * Remove sapnwrfc trace file.
     */
    public function tearDown()
    {
        $traceFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dev_rfc.trc';
        if (file_exists($traceFile)) {
            unlink($traceFile);
        }
        parent::tearDown();
    }
}
