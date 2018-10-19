<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System;

/**
 * Interface which describes a service to load specific dropdown values from a given provider
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface DropdownLoaderInterface
{
    /**
     * This method returns an array of dropdown values which are associated with the provided provider string. The
     * implementation uses the provider string to fetch the fitting values. Optional an implementation can use the
     * params property for specific config values i.e. the config loader has a module parameter
     *
     * @param string $provider
     * @param array $params
     * @return array
     */
    public function fetchValues(string $provider, array $params = []) : array;
}
