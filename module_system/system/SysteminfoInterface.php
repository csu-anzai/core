<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\System;

/**
 * Interface for systeminfo plugins.
 * Rendered when opening the systeminformation in the backend
 *
 * @package module_system
 * @since 4.5
 * @author sidler@mulchprod.de
 */
interface SysteminfoInterface extends GenericPluginInterface
{

    const STR_EXTENSION_POINT = "core.system.systeminfo";

    /**
     * Returns the title of the info-block
     *
     * @return string
     */
    public function getStrTitle();

    /**
     * Returns the contents of the info-block
     *
     * @return array
     */
    public function getArrContent();
}