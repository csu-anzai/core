<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System;

/**
 * Dropdown loader implementation which reads the values from the config
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class DropdownConfigLoader implements DropdownLoaderInterface
{
    /**
     * @inheritdoc
     */
    public function fetchValues(string $provider, array $params = []) : array
    {
        $module = $params["module"] ?? null;
        $configFile = $params["config"] ?? null;

        if (empty($module)) {
            throw new \RuntimeException("Dropdown provider annotation has no module parameter");
        }

        // in case we have no explicit config file use default config.php
        if (empty($configFile)) {
            $configFile = "config.php";
        }

        $values = $this->getConfig($module, $configFile, $provider);

        if (!is_array($values)) {
            throw new \RuntimeException("Dropdown provider config value must be an array");
        }

        return $values;
    }

    /**
     * @param string $module
     * @param string $configFile
     * @param string $provider
     * @return string
     */
    protected function getConfig($module, $configFile, $provider)
    {
        return Config::getInstance($module, $configFile)->getConfig($provider);
    }
}
