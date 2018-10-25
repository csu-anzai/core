<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Oauth2\Installer;

use AGP\Wpsapi\Admin\WpsApiController;
use AGP\Wpsapi\System\Models\ShoppingCart;
use Kajona\Oauth2\Admin\OAuth2Controller;
use Kajona\System\System\InstallerBase;
use Kajona\System\System\InstallerRemovableInterface;
use Kajona\System\System\OrmSchemamanager;
use Kajona\System\System\SystemModule;

/**
 * Class providing an installer for the oauth2 module
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class InstallerOAuth2 extends InstallerBase implements InstallerRemovableInterface
{
    /**
     * @inheritdoc
     */
    public function install()
    {
        $strReturn = "";
        $strReturn .= "Registering module...\n";
        $this->registerModule($this->objMetadata->getStrTitle(), _oauth2_module_id_, "", OAuth2Controller::class, $this->objMetadata->getStrVersion(), false);

        return $strReturn;
    }

    /**
     * @inheritdoc
     */
    public function isRemovable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function remove(&$strReturn)
    {
        //delete all reocrds
        //TODO

        //delete the module-node
        $strReturn .= "Deleting the module-registration...\n";
        $objModule = SystemModule::getModuleByName($this->objMetadata->getStrTitle(), true);
        if (!$objModule->deleteObject()) {
            $strReturn .= "Error deleting module, aborting.\n";
            return false;
        }

        //delete the tables
        //TODO


        return true;
    }

    /**
     * @inheritdoc
     */
    public function update()
    {
        $strReturn = "";
        //check installed version and to which version we can update
        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        $strReturn .= "Version found:\n\t Module: ".$arrModule["module_name"].", Version: ".$arrModule["module_version"]."\n\n";

        return $strReturn."\n\n";
    }
}
