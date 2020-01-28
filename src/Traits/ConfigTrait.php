<?php

namespace phpsap\saprfc\Traits;

use phpsap\interfaces\Config\IConfigCommon;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\exceptions\IIncompleteConfigException;

/**
 * Trait ConfigTrait
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait ConfigTrait
{
    /**
     * Get the module specific connection configuration.
     * @param \phpsap\interfaces\Config\IConfiguration $config
     * @return array
     * @throws IIncompleteConfigException
     */
    protected function getModuleConfig(IConfiguration $config)
    {
        $common = $this->getCommonConfig($config);
        /**
         * Only type A and B configurations are supported by this module,
         * its common classes and its interface. Therefore, we do not
         * expect any other types here.
         */
        if ($config instanceof IConfigTypeA) {
            $specific = $this->getTypeAConfig($config);
        } else {
            $specific = $this->getTypeBConfig($config);
        }
        return array_merge($common, $specific);
    }

    /**
     * Get the common configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @param IConfigCommon $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getCommonConfig(IConfigCommon $config)
    {
        $common = [];
        if ($config->getLang() !== null) {
            $common['LANG'] = $config->getLang();
        }
        if ($config->getTrace() !== null) {
            $common['TRACE'] = true;
        }
        $common['CLIENT'] = $config->getClient();
        $common['USER'] = $config->getUser();
        $common['PASSWD'] = $config->getPasswd();
        return $common;
    }

    /**
     * Get the connection type A configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @param IConfigTypeA $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getTypeAConfig(IConfigTypeA $config)
    {
        $typeA = [];
        if ($config->getGwhost() !== null) {
            $typeA['GWHOST'] = $config->getGwhost();
        }
        if ($config->getGwserv() !== null) {
            $typeA['GWSERV'] = $config->getGwserv();
        }
        $typeA['ASHOST'] = $config->getAshost();
        $typeA['SYSNR']  = $config->getSysnr();
        return $typeA;
    }

    /**
     * Get the connection type B configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @param IConfigTypeB $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getTypeBConfig(IConfigTypeB $config)
    {
        $typeB = [];
        if ($config->getR3name() !== null) {
            $typeB['R3NAME'] = $config->getR3name();
        }
        if ($config->getGroup() !== null) {
            $typeB['GROUP'] = $config->getGroup();
        }
        $typeB['MSHOST'] = $config->getMshost();
        return $typeB;
    }
}
