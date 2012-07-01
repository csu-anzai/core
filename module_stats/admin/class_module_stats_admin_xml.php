<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_modul_stats_admin_xml.php 3952 2011-06-26 12:13:25Z sidler $                             *
********************************************************************************************************/


/**
 * Admin class of the stats-module - xml based.
 * Triggers the report-generation 
 *
 * @package module_stats
 * @author sidler@mulchprod.de
 */
class class_module_stats_admin_xml extends class_admin implements interface_xml_admin {

    /**
     * @var class_date
     */
	private $objDateStart;
    /**
     * @var class_date
     */
	private $objDateEnd;
	private $intInterval;
    

	/**
	 * Constructor
	 */
	public function __construct() {
        $this->setArrModuleEntry("modul", "stats");
        $this->setArrModuleEntry("moduleId", _stats_modul_id_);
        parent::__construct();

        
        $intDateStart = class_carrier::getInstance()->getObjSession()->getSession(class_module_stats_admin::$STR_SESSION_KEY_DATE_START);
		//Start: first day of current month
        $this->objDateStart = new class_date();
        $this->objDateStart->setTimeInOldStyle($intDateStart);
        
		//End: Current Day of month
        $intDateEnd = class_carrier::getInstance()->getObjSession()->getSession(class_module_stats_admin::$STR_SESSION_KEY_DATE_END);
        $this->objDateEnd = new class_date();
        $this->objDateEnd->setTimeInOldStyle($intDateEnd);

        
        $this->intInterval = class_carrier::getInstance()->getObjSession()->getSession(class_module_stats_admin::$STR_SESSION_KEY_INTERVAL);
	}

    
    /**
     * Triggers the "real" creation of the report and wraps the code inline into a xml-structure
     * 
     * @return string
     * @permissions view
     */
    protected function actionGetReport() {
        $strPlugin = $this->getParam("plugin");
        $strReturn = "";
        $objFilesystem = new class_filesystem();
        $arrPlugins = class_resourceloader::getInstance()->getFolderContent("/admin/statsreports", array(".php"));

        foreach($arrPlugins as $strOnePlugin) {
            $strClassName = str_replace(".php", "", $strOnePlugin);
            /** @var $objPlugin interface_admin_statsreports */
            $objPlugin = new $strClassName($this->objDB, $this->objToolkit, $this->getObjLang());

            if($objPlugin->getReportCommand() == $strPlugin && $objPlugin instanceof interface_admin_statsreports) {
                //get date-params as ints
                $intStartDate = mktime(0, 0, 0, $this->objDateStart->getIntMonth() , $this->objDateStart->getIntDay(), $this->objDateStart->getIntYear());
                $intEndDate = mktime(0, 0, 0, $this->objDateEnd->getIntMonth() , $this->objDateEnd->getIntDay(), $this->objDateEnd->getIntYear());
                $objPlugin->setEndDate($intEndDate);
                $objPlugin->setStartDate($intStartDate);
                $objPlugin->setInterval($this->intInterval);

                $arrImage = $objPlugin->getReportGraph();

                if(!is_array($arrImage))
                    $arrImage = array($arrImage);

                foreach($arrImage as $strImage) {
                    if($strImage != "") {
                       $strReturn .= $this->objToolkit->getGraphContainer($strImage."?reload=".time());
                    }
                }


                $strReturn .=  $objPlugin->getReport();
                $strReturn =  "<content><![CDATA[" .$strReturn. "]]></content>";
            }
        }

        return $strReturn;
    }
	

}

