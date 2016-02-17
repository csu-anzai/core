<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Packageserver\Admin\Statsreports;

use \Kajona\System\System\Date;
use class_db;
use class_graph_factory;
use class_lang;
use class_module_user_user;
use class_session;
use class_toolkit_admin;
use interface_admin_statsreports;
use Kajona\System\Admin\ToolkitAdmin;
use Kajona\System\System\Database;
use Kajona\System\System\Lang;


/**
 * Plugin to module stats, ploting a list of most active query-sources
 *
 * @author sidler@mulchprod.de
 */
class StatsReportPackageserverqueries implements interface_admin_statsreports
{

    //class vars
    private $intDateStart;
    private $intDateEnd;
    private $intInterval;

    private $objLang;
    private $objToolkit;
    private $objDB;


    /**
     * Constructor
     */
    public function __construct(Database $objDB, ToolkitAdmin $objToolkit, Lang $objTexts)
    {
        $this->objLang = $objTexts;
        $this->objToolkit = $objToolkit;
        $this->objDB = $objDB;
    }

    /**
     * Returns the name of extension/plugin the objects wants to contribute to.
     *
     * @return string
     */
    public static function getExtensionName()
    {
        return "core.stats.admin.statsreport";
    }

    /**
     * @param int $intEndDate
     *
     * @return void
     */
    public function setEndDate($intEndDate)
    {
        $this->intDateEnd = $intEndDate;
    }

    /**
     * @param int $intStartDate
     *
     * @return void
     */
    public function setStartDate($intStartDate)
    {
        $this->intDateStart = $intStartDate;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->objLang->getLang("packageservertopqueries", "packageserver");
    }

    /**
     * @return bool
     */
    public function isIntervalable()
    {
        return true;
    }

    /**
     * @param int $intInterval
     *
     * @return void
     */
    public function setInterval($intInterval)
    {
        $this->intInterval = $intInterval;
    }

    /**
     * @return string
     */
    public function getReport()
    {
        $strReturn = "";

        $arrData = $this->getTotalUniqueHostsInInterval();

        $arrLogs = array();
        $intI = 0;
        $objUser = new class_module_user_user(class_session::getInstance()->getUserID());
        foreach ($arrData as $arrOneLog) {
            if ($intI++ >= $objUser->getIntItemsPerPage()) {
                break;
            }

            $arrLogs[$intI][0] = $intI;
            $arrLogs[$intI][1] = $arrOneLog["log_hostname"];
            $arrLogs[$intI][2] = $arrOneLog["anzahl"];
        }
        //Create a data-table
        $arrHeader = array();
        $arrHeader[0] = "#";
        $arrHeader[1] = $this->objLang->getLang("packageservertopqueries_header_host", "packageserver");
        $arrHeader[2] = $this->objLang->getLang("packageservertopqueries_header_requests", "packageserver");
        $strReturn .= $this->objToolkit->dataTable($arrHeader, $arrLogs);

        return $strReturn;
    }

    /**
     * Returns the pages and their hits
     *
     * @return mixed
     */
    public function getTotalHitsInInterval()
    {
        $objStart = new \Kajona\System\System\Date($this->intDateStart);
        $objEnd = new \Kajona\System\System\Date($this->intDateEnd);
        $strQuery = "SELECT COUNT(*)
						FROM "._dbprefix_."packageserver_log
						WHERE log_date > ?
						  AND log_date <= ?";

        $arrRow = $this->objDB->getPRow($strQuery, array($objStart->getLongTimestamp(), $objEnd->getLongTimestamp()));

        return $arrRow["COUNT(*)"];
    }

    /**
     * @return int
     */
    public function getTotalUniqueHitsInInterval()
    {
        return count($this->getTotalUniqueHostsInInterval());
    }

    /**
     * @return array
     */
    public function getTotalUniqueHostsInInterval()
    {
        $objStart = new \Kajona\System\System\Date($this->intDateStart);
        $objEnd = new \Kajona\System\System\Date($this->intDateEnd);
        $strQuery = "SELECT log_hostname, COUNT(*) as anzahl
						FROM "._dbprefix_."packageserver_log
						WHERE log_date > ?
						  AND log_date <= ?
				     GROUP BY log_hostname
				     ORDER BY anzahl DESC";

        $arrRow = $this->objDB->getPArray($strQuery, array($objStart->getLongTimestamp(), $objEnd->getLongTimestamp()));

        return $arrRow;
    }

    /**
     * @return array
     */
    public function getReportGraph()
    {
        $arrReturn = array();

        $arrTickLabels = array();

        $intGlobalEnd = $this->intDateEnd;
        $intGlobalStart = $this->intDateStart;

        $this->intDateEnd = $this->intDateStart + 60 * 60 * 24 * $this->intInterval;

        $intCount = 0;
        $arrTotalHits = array();
        $arrUniqueHits = array();

        while ($this->intDateStart <= $intGlobalEnd) {
            $arrTotalHits[$intCount] = $this->getTotalHitsInInterval();
            $arrUniqueHits[$intCount] = $this->getTotalUniqueHitsInInterval();
            $arrTickLabels[$intCount] = date("d.m.", $this->intDateStart);
            //increase start & end-date
            $this->intDateStart = $this->intDateEnd;
            $this->intDateEnd = $this->intDateStart + 60 * 60 * 24 * $this->intInterval;
            $intCount++;
        }
        //create graph
        if ($intCount > 1) {
            $objGraph = class_graph_factory::getGraphInstance();
            $objGraph->setArrXAxisTickLabels($arrTickLabels);
            $objGraph->addLinePlot($arrTotalHits, $this->objLang->getLang("packageservertopqueries_total", "packageserver"));
            $objGraph->addLinePlot($arrUniqueHits, $this->objLang->getLang("packageservertopqueries_unique", "packageserver"));
            $objGraph->setIntWidth(815);
            $objGraph->renderGraph();
            $arrReturn[] = $objGraph->renderGraph();
        }
        //reset global dates
        $this->intDateEnd = $intGlobalEnd;
        $this->intDateStart = $intGlobalStart;

        return $arrReturn;

    }

}

