<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/
declare(strict_types=1);

namespace Kajona\Tinyurl\Installer;

use Kajona\System\System\DbDatatypes;
use Kajona\System\System\InstallerBase;
use Kajona\System\System\InstallerRemovableInterface;
use Kajona\System\System\SystemModule;
use Kajona\Tinyurl\Admin\TinyUrlController;

/**
 * Class providing an install for the TinyUrl module
 *
 * @package module_tinyurl
 * @author andrii.konoval@artemeon.de
 * @moduleId _tinyurl_module_id_
 */
class InstallerTinyurl extends InstallerBase implements InstallerRemovableInterface {

    public function install() {
        $strReturn = "";
        $strReturn .= "Installing table tinyurl...\n";
        $arrFields = array();
        $arrFields["url_id"]    = array(DbDatatypes::STR_TYPE_CHAR20, false);
        $arrFields["url"]       = array(DbDatatypes::STR_TYPE_LONGTEXT, true);

        if(!$this->objDB->createTable("agp_tinyurl", $arrFields, array("url_id"))) {
            $strReturn .= "An error occurred! ...\n";
        }

        //register the module
        $strReturn .= "Register the module...\n";
        $this->registerModule(
            "tinyurl",
            _tinyurl_module_id_,
            "",
            TinyUrlController::class,
            $this->objMetadata->getStrVersion()
        );

        return $strReturn;

    }

    public function update()
    {
        $strReturn = "";
        return $strReturn."\n\n";
    }

    public function remove(&$strReturn)
    {
        //delete the module-node
        $strReturn .= "Deleting the module-registration...\n";
        $objModule = SystemModule::getModuleByName($this->objMetadata->getStrTitle(), true);
        if(!$objModule->deleteObjectFromDatabase()) {
            $strReturn .= "Error deleting module, aborting.\n";
            return false;
        }

        //delete the tables
        $strReturn .= "Dropping table agp_tiny_url";
        if (!$this->objDB->_pQuery("DROP TABLE agp_tinyurl", array())) {
            $strReturn .= "Error deleting table, aborting.\n";
            return false;
        }

        return true;
    }

    public function isRemovable()
    {
        return true;
    }


}
