<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                *
********************************************************************************************************/

namespace Kajona\Tags\Installer;

use Kajona\System\System\InstallerBase;
use Kajona\System\System\InstallerRemovableInterface;
use Kajona\System\System\OrmSchemamanager;
use Kajona\System\System\SystemModule;
use Kajona\System\System\SystemSetting;
use Kajona\Tags\System\TagsFavorite;
use Kajona\Tags\System\TagsTag;

/**
 * Class providing an install for the tags module
 *
 * @package module_tags
 * @author sidler@mulchprod.de
 * @moduleId _tags_modul_id_
 */
class InstallerTags extends InstallerBase implements InstallerRemovableInterface {

    public function install() {
		$strReturn = "";
        $objManager = new OrmSchemamanager();

		//tags_tag --------------------------------------------------------------------------------------
		$strReturn .= "Installing table tags_tag...\n";
        $objManager->createTable(TagsTag::class);

		$strReturn .= "Installing table tags_member...\n";
        $arrFields = array();
		$arrFields["tags_memberid"]     = array("char20", false);
		$arrFields["tags_systemid"] 	= array("char20", false);
		$arrFields["tags_tagid"]        = array("char20", false);
		$arrFields["tags_attribute"]    = array("char254", true);
		$arrFields["tags_owner"]        = array("char20", true);

		if(!$this->objDB->createTable("agp_tags_member", $arrFields, array("tags_memberid"), array("tags_systemid", "tags_tagid", "tags_attribute", "tags_owner")))
			$strReturn .= "An error occurred! ...\n";

        $strReturn .= "Installing table tags_favorite...\n";
        $objManager->createTable(TagsFavorite::class);

		//register the module
		$this->registerModule(
            "tags",
            _tags_modul_id_,
            "",
            "TagsAdmin.php",
            $this->objMetadata->getStrVersion()
        );

		$strReturn .= "Registering system-constants...\n";
        $this->registerConstant("_tags_defaultprivate_", "false", SystemSetting::$int_TYPE_BOOL, _tags_modul_id_);

		return $strReturn;

	}

    /**
     * Validates whether the current module/element is removable or not.
     * This is the place to trigger special validations and consistency checks going
     * beyond the common metadata-dependencies.
     *
     * @return bool
     */
    public function isRemovable() {
        return true;
    }

    /**
     * Removes the elements / modules handled by the current installer.
     * Use the reference param to add a human readable logging.
     *
     * @param string &$strReturn
     *
     * @return bool
     */
    public function remove(&$strReturn) {

        $strReturn .= "Removing settings...\n";
        if(SystemSetting::getConfigByName("_tags_defaultprivate_") != null)
            SystemSetting::getConfigByName("_tags_defaultprivate_")->deleteObjectFromDatabase();


        /** @var TagsFavorite $objOneObject */
        foreach(TagsFavorite::getObjectListFiltered() as $objOneObject) {
            $strReturn .= "Deleting object '".$objOneObject->getStrDisplayName()."' ...\n";
            if(!$objOneObject->deleteObjectFromDatabase()) {
                $strReturn .= "Error deleting object, aborting.\n";
                return false;
            }
        }

        /** @var TagsTag $objOneObject */
        foreach(TagsTag::getObjectListFiltered() as $objOneObject) {
            $strReturn .= "Deleting object '".$objOneObject->getStrDisplayName()."' ...\n";
            if(!$objOneObject->deleteObjectFromDatabase()) {
                $strReturn .= "Error deleting object, aborting.\n";
                return false;
            }
        }

        //delete the module-node
        $strReturn .= "Deleting the module-registration...\n";
        $objModule = SystemModule::getModuleByName($this->objMetadata->getStrTitle(), true);
        if(!$objModule->deleteObjectFromDatabase()) {
            $strReturn .= "Error deleting module, aborting.\n";
            return false;
        }

        //delete the tables
        foreach(array("tags_tag", "tags_member", "tags_favorite") as $strOneTable) {
            $strReturn .= "Dropping table ".$strOneTable."...\n";
            if(!$this->objDB->_pQuery("DROP TABLE ".$this->objDB->encloseTableName($strOneTable), array())) {
                $strReturn .= "Error deleting table, aborting.\n";
                return false;
            }

        }

        return true;
    }


    public function update() {
	    $strReturn = "";
        //check installed version and to which version we can update
        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        $strReturn .= "Version found:\n\t Module: ".$arrModule["module_name"].", Version: ".$arrModule["module_version"]."\n\n";

        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        if($arrModule["module_version"] == "6.2") {
            $strReturn .= "Updating to 6.5...\n";
            $this->updateModuleVersion($this->objMetadata->getStrTitle(), "6.5");
        }

        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        if($arrModule["module_version"] == "6.5") {
            $strReturn .= "Updating to 6.6...\n";
            $this->updateModuleVersion($this->objMetadata->getStrTitle(), "6.6");
        }

        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        if($arrModule["module_version"] == "6.6") {
            $strReturn .= "Updating to 7.0...\n";
            $this->updateModuleVersion($this->objMetadata->getStrTitle(), "7.0");
        }

        return $strReturn."\n\n";
	}


}
