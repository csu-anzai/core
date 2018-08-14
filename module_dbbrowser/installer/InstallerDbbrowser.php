<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Dbbrowser\Installer;

use Kajona\Dbbrowser\Admin\DbbrowserController;
use Kajona\System\System\InstallerBase;
use Kajona\System\System\InstallerRemovableInterface;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemModule;

/**
 *
 * @author stefan.idler@artemeon.de
 * @moduleId _dbbrowser_module_id_
 */
class InstallerDbbrowser extends InstallerBase implements InstallerRemovableInterface
{

    public function install()
    {
        $strReturn = "";

        //register the module
        $strReturn .= "Register module".PHP_EOL;
        $this->registerModule("dbbrowser", _dbbrowser_module_id_, "", DbbrowserController::class, $this->objMetadata->getStrVersion(), true);

        $strReturn .= "Assigning moduls to system aspects...\n";
        $objModule = SystemModule::getModuleByName("dbbrowser");
        $objModule->setStrAspect(SystemAspect::getAspectByName("management")->getSystemid());
        ServiceLifeCycleFactory::getLifeCycle(get_class($objModule))->update($objModule);

        return $strReturn;

    }

    /**
     * Validates whether the current module/element is removable or not.
     * This is the place to trigger special validations and consistency checks going
     * beyond the common metadata-dependencies.
     *
     * @return bool
     */
    public function isRemovable()
    {
        return true;
    }

    /**
     * Removes the elements / modules handled by the current installer.
     * Use the reference param to add a human readable logging.
     *
     * @param string &$strReturn
     *
     * @return bool
     * @throws \Kajona\System\System\Exception
     */
    public function remove(&$strReturn)
    {
        //delete the module-node
        $strReturn .= "Deleting the module-registration...\n";
        $objModule = SystemModule::getModuleByName($this->objMetadata->getStrTitle(), true);
        if (!$objModule->deleteObjectFromDatabase()) {
            $strReturn .= "Error deleting module, aborting.\n";
            return false;
        }

        return true;
    }


    public function update()
    {
        $strReturn = "";
        //check installed version and to which version we can update
        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        $strReturn .= "Version found:\n\t Module: ".$arrModule["module_name"].", Version: ".$arrModule["module_version"]."\n\n";

        return $strReturn."\n\n";
    }

}
