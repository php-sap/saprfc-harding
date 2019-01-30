<?php
/**
 * File src/SapRfcConfigTrait.php
 *
 * Common code for connection configuration.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\exceptions\IncompleteConfigException;

/**
 * Trait SapRfcConfigTrait
 *
 * Common code for connection configuration. Implements methods of
 * phpsap\classes\AbstractConfigContainer.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait SapRfcConfigTrait
{
    /**
     * Generate the configuration array needed for connecting a remote SAP system
     * using Piers Hardings sapnwrfc module.
     * @return array
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function generateConfig()
    {
        $config = [];
        foreach (static::$conParamAvail as $key => $mandatory) {
            $keyLower = strtolower($key);
            if ($this->has($keyLower)) {
                $method = sprintf('get%s', ucfirst($keyLower));
                $config[$key] = $this->{$method}();
                if ($key === 'TRACE') {
                    $config[$key] = $config[$key] > 0 ? true : false;
                }
            } elseif ($mandatory === true) {
                throw new IncompleteConfigException(sprintf(
                    'Missing mandatory key %s.',
                    $keyLower
                ));
            }
        }
        return $config;
    }
}
