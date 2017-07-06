<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Workflows\System;
use Kajona\System\System\Carrier;
use Kajona\System\System\Date;

/**
 * Helper to generate some statistical information
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 */
class WorkflowsStats
{

    /**
     * Fetches all controller runs for a date
     * @param Date $objDate
     * @param int $intStart
     * @param int $intEnd
     * @return array
     */
    public function getControllerForDate(Date $objDate, int $intStart, int $intEnd): array
    {
        $objDb = Carrier::getInstance()->getObjDB();
        return $objDb->getPArray(
            "SELECT controller.*, (SELECT COUNT(*) AS anz FROM "._dbprefix_."workflows_stat_wfh WHERE wfh_wfc = controller.wfc_id ) as anzhandler FROM "._dbprefix_."workflows_stat_wfc AS controller WHERE wfc_start >= ? AND wfc_start <= ? ORDER BY wfc_start",
            [$objDate->setBeginningOfDay()->getLongTimestamp(), $objDate->setEndOfDay()->getLongTimestamp()],
            $intStart,
            $intEnd
        );
    }

    /**
     * Counts the controller runs per date
     * @param Date $objDate
     * @return int
     */
    public function getControllerForDateCount(Date $objDate): int
    {
        $objDb = Carrier::getInstance()->getObjDB();
        return $objDb->getPRow(
            "SELECT COUNT(*) AS cnt FROM "._dbprefix_."workflows_stat_wfc WHERE wfc_start >= ? AND wfc_start <= ?",
            [$objDate->setBeginningOfDay()->getLongTimestamp(), $objDate->setEndOfDay()->getLongTimestamp()]
        )["cnt"];
    }

    /**
     * Fetches all executed handlers for a single controller run
     * @param string $strSystemid
     * @return array
     */
    public function getHandlerForController(string $strSystemid): array
    {
        $objDb = Carrier::getInstance()->getObjDB();
        return $objDb->getPArray(
            "SELECT * FROM "._dbprefix_."workflows_stat_wfh WHERE wfh_wfc = ? ORDER BY wfh_start",
            [$strSystemid]
        );
    }

    /**
     * Generates an array of statistical values on a hourly base
     * @param Date $objDate
     * @return array
     */
    public function getHourlyStats(Date $objDate): array
    {
        $objStart = clone $objDate;
        $objStart->setBeginningOfDay();
        $objEnd = clone $objStart;
        $objEnd->setIntMin(59)->setIntSec(59);

        $objDb = Carrier::getInstance()->getObjDB();

        $arrProcessedController = [];
        $arrBrokenController = [];
        $arrHandlers = [];

        for ($intI = 0; $intI <= 23; $intI++) {
            $objStart->setIntHour($intI);
            $objEnd->setIntHour($intI);

            $strKey = $intI.":00";

            $arrProcessedController[$strKey] = $objDb->getPRow("SELECT COUNT(*) as anz FROM "._dbprefix_."workflows_stat_wfc WHERE wfc_start >= ? AND wfc_start <= ? AND wfc_end IS NOT NULL", [$objStart, $objEnd])["anz"];
            $arrBrokenController[$strKey]    = $objDb->getPRow("SELECT COUNT(*) as anz FROM "._dbprefix_."workflows_stat_wfc WHERE wfc_start >= ? AND wfc_start <= ? AND wfc_end IS NULL", [$objStart, $objEnd])["anz"];
            $arrHandlers[$strKey]            = $objDb->getPRow("SELECT COUNT(*) as anz FROM "._dbprefix_."workflows_stat_wfh WHERE wfh_start >= ? AND wfh_start <= ?", [$objStart, $objEnd])["anz"];

        }

        return [$arrProcessedController, $arrBrokenController, $arrHandlers];
    }

}
