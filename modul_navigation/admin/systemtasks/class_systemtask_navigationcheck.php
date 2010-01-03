<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2010 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                        *
********************************************************************************************************/

/**
 * Checkes the existing navigation-points for valid internal links.
 *
 * @package modul_navigation
 */
class class_systemtask_navigationcheck extends class_systemtask_base implements interface_admin_systemtask {


    /**
     * contructor to call the base constructor
     */
    public function __construct() {
        parent::__construct();
        //set the correct text-base
        $this->setStrTextBase("navigation");
    }

    /**
     * @see interface_admin_systemtast::getGroupIdenitfier()
     * @return string
     */
    public function getGroupIdentifier() {
        return "";
    }

    /**
     * @see interface_admin_systemtast::getStrInternalTaskName()
     * @return string
     */
    public function getStrInternalTaskName() {
        return "navigationcheck";
    }

    /**
     * @see interface_admin_systemtast::getStrTaskName()
     * @return string
     */
    public function getStrTaskName() {
        return $this->getText("systemtask_navigationcheck_name");
    }

    /**
     * @see interface_admin_systemtast::executeTask()
     * @return string
     */
    public function executeTask() {
        $strReturn = "";

        //load all navigation points, tree by tree
        $arrTrees = class_modul_navigation_tree::getAllNavis();
        foreach($arrTrees as $objOneTree) {
            $strReturn .= $this->getText("systemtask_navigationcheck_treescan")." \"".$objOneTree->getStrName()."\"...<br />";
            $strReturn .= $this->processLevel($objOneTree->getSystemid(), 0)."<br />";
        }

        return $strReturn;
    }

    private function processLevel($intParentId, $intLevel) {
        $strReturn = "";
        $arrNaviPoints = class_modul_navigation_point::getNaviLayer($intParentId);
        foreach($arrNaviPoints as $objOnePoint) {
            for($intI = 0; $intI<=$intLevel; $intI++)
                $strReturn .= "&nbsp; &nbsp;";

            $strReturn .= $this->processSinglePoint($objOnePoint);
            $strReturn .= $this->processLevel($objOnePoint->getSystemid(), $intLevel+1);
        }

        return $strReturn;
    }


    private function processSinglePoint($objPoint) {
        $strReturn = "";
        $bitError = false;

        $strReturn .= $objPoint->getStrName().": ";

        if($objPoint->getStrPageI() == "" && $objPoint->getStrPageE() == "") {
            $strReturn .= $this->getText("systemtask_navigationcheck_invalidEmpty");
            $bitError = true;
        }
        else if($objPoint->getStrPageI() != "" && $objPoint->getStrPageE() != "") {
            $strReturn .= $this->getText("systemtask_navigationcheck_invalidBoth");
            $bitError = true;
        }
        else if($objPoint->getStrPageI() != "" && $objPoint->getStrPageE() == "") {
            //try to load internal page and check if it exists
            $objPage = class_modul_pages_page::getPageByName($objPoint->getStrPageI());

            if($objPage->getSystemid() == "") {
                $strReturn .= $this->getText("systemtask_navigationcheck_invalidInternal")." ".$objPoint->getStrPageI().")";
                $bitError = true;
            } else {
                $strReturn .= $this->getText("systemtask_navigationcheck_valid")." ".$objPoint->getStrPageI(). $objPoint->getStrPageE().")";
            }
        }
        else {
            $strReturn .= $this->getText("systemtask_navigationcheck_valid")." ".$objPoint->getStrPageI(). $objPoint->getStrPageE().")";
        }

        if ($bitError) {
            $strReturn = "<b>".$strReturn."</b>";
        }

        return $strReturn."<br />";
    }

    /**
     * @see interface_admin_systemtask::getAdminForm()
     * @return string
     */
    public function getAdminForm() {
        return "";
    }

}
?>