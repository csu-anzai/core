<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                           *
********************************************************************************************************/

namespace Kajona\Flow\Installer;

use AGP\Agp_Commons\System\ArtemeonCommon;
use Kajona\Flow\System\FlowActionAbstract;
use Kajona\Flow\System\FlowConditionAbstract;
use Kajona\Flow\System\FlowConfig;
use Kajona\Flow\System\FlowStatus;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\InstallerBase;
use Kajona\System\System\OrmSchemamanager;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemModule;

/**
 * Class providing an install for the news module
 *
 * @package module_flow
 * @moduleId _flow_module_id_
 */
class InstallerFlow extends InstallerBase
{
    public function install()
    {
        $strReturn = "";
        $objManager = new OrmSchemamanager();

        $strReturn .= "Installing table flow ...\n";
        $objManager->createTable(FlowConfig::class);

        $strReturn .= "Installing table flow_step...\n";
        $objManager->createTable(FlowStatus::class);

        $strReturn .= "Installing table flow_step_transition...\n";
        $objManager->createTable(FlowTransition::class);

        $strReturn .= "Installing table flow_step_transition_action...\n";
        $objManager->createTable(FlowActionAbstract::class);

        $strReturn .= "Installing table flow_step_transition_condition...\n";
        $objManager->createTable(FlowConditionAbstract::class);

        //register the module
        $this->registerModule(
            "flow",
            _flow_module_id_,
            "",
            "FlowAdmin.php",
            $this->objMetadata->getStrVersion()
        );

        // sync all handler classes
        FlowConfig::syncHandler();

        return $strReturn;
    }

    public function update()
    {
        $strReturn = "";
        //check installed version and to which version we can update
        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        $strReturn .= "Version found:\n\t Module: ".$arrModule["module_name"].", Version: ".$arrModule["module_version"]."\n\n";

        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        if ($arrModule["module_version"] == "7.0") {
            $strReturn .= $this->update_70_701();
        }

        $arrModule = SystemModule::getPlainModuleData($this->objMetadata->getStrTitle(), false);
        if ($arrModule["module_version"] == "7.0.1") {
            $strReturn = "Updating to 7.1...\n";
            $this->updateModuleVersion($this->objMetadata->getStrTitle(), "7.1");
        }

        return $strReturn;
    }

    private function update_70_701()
    {
        $strReturn = "Updating to 7.0.1...\n";

        // add step roles column
        $strReturn.= "Add roles column...\n";
        $this->objDB->addColumn("agp_flow_step", "step_roles", DbDatatypes::STR_TYPE_TEXT);

        $this->updateModuleVersion($this->objMetadata->getStrTitle(), "7.0.1");
        return $strReturn;
    }
}
