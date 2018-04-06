<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$					    *
********************************************************************************************************/

namespace Kajona\Workflows\System;

use Kajona\System\System\Carrier;
use Kajona\System\System\Date;
use Kajona\System\System\Logger;


/**
 * The controller triggers the execution of scheduled workflows and manages the transition of
 * workflows' states.
 *
 * @package module_workflows
 * @author sidler@mulchprod.de
 */
class WorkflowsController
{

    const STR_LOGFILE = "workflows.log";

    private $strProcessId = "";


    /**
     * Single entry into the processing of workflows.
     * Schedules and executes outstanding workflows internally.
     * Creates some stats about the execution times and number of handlers processed.
     */
    public function processWorkflows()
    {
        $this->strProcessId = generateSystemid();
        $objDb = Carrier::getInstance()->getObjDB();

        $objDb->_pQuery("INSERT INTO agp_workflows_stat_wfc (wfc_id, wfc_start) VALUES (?,?)", [$this->strProcessId, new Date()]);

        $this->scheduleWorkflows();

        if (!defined("_workflow_is_running_")) {
            define("_workflow_is_running_", true);
        }

        $this->runWorkflows();

        $objDb->_pQuery("UPDATE agp_workflows_stat_wfc SET wfc_end = ? where wfc_id = ?", [new Date(), $this->strProcessId]);
    }


    /**
     * Searches for new workflows and forces them to schedule and initialize
     */
    private function scheduleWorkflows()
    {
        $arrWorkflows = WorkflowsWorkflow::getWorkflowsByType(WorkflowsWorkflow::$INT_STATE_NEW, false);

        Logger::getInstance(self::STR_LOGFILE)->info("scheduling workflows, count: ".count($arrWorkflows));

        foreach ($arrWorkflows as $objOneWorkflow) {
            if ($objOneWorkflow->getIntRecordStatus() == 0) {
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." is inactive, can't be scheduled");
                continue;
            }

            //lock the workflow
            $objLockmanager = $objOneWorkflow->getLockManager();
            if ($objLockmanager->isLocked()) {
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." is locked, can't be scheduled");
                continue;
            }

            $objLockmanager->lockRecord();

            /**
             * @var WorkflowsHandlerInterface
             */
            $objHandler = $objOneWorkflow->getObjWorkflowHandler();

            //trigger the workflow
            Logger::getInstance(self::STR_LOGFILE)->info("scheduling workflow ".$objOneWorkflow->getSystemid());
            if ($objOneWorkflow->getObjTriggerdate() == null) {
                $objOneWorkflow->setObjTriggerdate(new \Kajona\System\System\Date());
            }
            $objHandler->schedule();

            Logger::getInstance(self::STR_LOGFILE)->info(" scheduling finished, new state: scheduled");
            $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_SCHEDULED);

            //init happened before
            $objOneWorkflow->updateObjectToDb();

            //unlock
            $objOneWorkflow->getLockManager()->unlockRecord(true);

        }

        Logger::getInstance(self::STR_LOGFILE)->info("scheduling workflows finished");
    }


    /**
     * Triggers the workflows scheduled for running.
     */
    private function runWorkflows()
    {
        $arrWorkflows = WorkflowsWorkflow::getWorkflowsByType(WorkflowsWorkflow::$INT_STATE_SCHEDULED);

        $objDb = Carrier::getInstance()->getObjDB();
        Logger::getInstance(self::STR_LOGFILE)->info("running workflows, count: ".count($arrWorkflows));


        foreach ($arrWorkflows as $objOneWorkflow) {
            $strWfRunId = generateSystemid();

            if ($objOneWorkflow->getIntRecordStatus() == 0) {
                $objDb->_pQuery(
                    "INSERT INTO agp_workflows_stat_wfh (wfh_id, wfh_wfc, wfh_start, wfh_end, wfh_class, wfh_result) VALUES (?,?,?,?,?,?)",
                    [$strWfRunId, $this->strProcessId, new Date(), new Date(), $objOneWorkflow->getStrClass(), WorkflowsResultEnum::INACTIVE()]
                );
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." is inactive, can't be executed");
                continue;
            }

            //lock the workflow
            $objLockmanager = $objOneWorkflow->getLockManager();
            if ($objLockmanager->isLocked()) {
                $objDb->_pQuery(
                    "INSERT INTO agp_workflows_stat_wfh (wfh_id, wfh_wfc, wfh_start, wfh_end, wfh_class, wfh_result) VALUES (?,?,?,?,?,?)",
                    [$strWfRunId, $this->strProcessId, new Date(), new Date(), $objOneWorkflow->getStrClass(), WorkflowsResultEnum::LOCKED()]
                );
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." is locked, can't be executed");
                continue;
            }

            //double-check if the workflow is still pending. it's possible that the workflow was fetched in the meantime by another thread.
            //so skip if the wf-state is either no longer scheduled or the nr of executions differ
            $arrRow = Carrier::getInstance()->getObjDB()->getPRow("SELECT * FROM agp_workflows WHERE workflows_id = ?", array($objOneWorkflow->getSystemid()), 0, false);
            if ($arrRow["workflows_state"] != WorkflowsWorkflow::$INT_STATE_SCHEDULED || $arrRow["workflows_runs"] != $objOneWorkflow->getIntRuns()) {
                $objDb->_pQuery(
                    "INSERT INTO agp_workflows_stat_wfh (wfh_id, wfh_wfc, wfh_start, wfh_end, wfh_class, wfh_result) VALUES (?,?,?,?,?,?)",
                    [$strWfRunId, $this->strProcessId, new Date(), new Date(), $objOneWorkflow->getStrClass(), WorkflowsResultEnum::PROCESSED_BY_OTHER_THREAD()]
                );
                Logger::getInstance(self::STR_LOGFILE)->info("skipping workflow ".$objOneWorkflow->getSystemid().", seems it was executed in the meantime");
                continue;
            }


            $bitReturn = $objLockmanager->lockRecord();
            if (!$bitReturn) {
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." cant lock workflow record");
                continue;
            }

            /**
             * @var WorkflowsHandlerInterface
             */
            $objHandler = $objOneWorkflow->getObjWorkflowHandler();

            //trigger the workflow
            Logger::getInstance(self::STR_LOGFILE)->info("executing workflow ".$objOneWorkflow->getSystemid());
            $objDb->_pQuery("INSERT INTO agp_workflows_stat_wfh (wfh_id, wfh_wfc, wfh_start, wfh_class) VALUES (?,?,?,?)", [$strWfRunId, $this->strProcessId, new Date(), $objOneWorkflow->getStrClass()]);


            try {
                if ($objHandler->execute()) {
                    //handler executed successfully. shift to state 'executed'
                    $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_EXECUTED);
                    Logger::getInstance(self::STR_LOGFILE)->info(" execution finished, new state: executed");
                    $objDb->_pQuery("UPDATE agp_workflows_stat_wfh SET wfh_end = ?, wfh_result = ? where wfh_id = ?", [new Date(), WorkflowsResultEnum::EXECUTE_FINISHED(), $strWfRunId]);
                } else {
                    //handler failed to execute. reschedule.
                    $objHandler->schedule();
                    $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_SCHEDULED);
                    Logger::getInstance(self::STR_LOGFILE)->info(" execution finished, new state: scheduled");
                    $objDb->_pQuery("UPDATE agp_workflows_stat_wfh SET wfh_end = ?, wfh_result = ? where wfh_id = ?", [new Date(), WorkflowsResultEnum::EXECUTE_SCHEDULED(), $strWfRunId]);
                }
            } catch (\Exception $objEx) {
                //fetch exceptions and reschedule the workflow - hopefully possible
                Logger::getInstance(self::STR_LOGFILE)->error(" execution failed, message: ".$objEx->getMessage());
                $objDb->_pQuery("UPDATE agp_workflows_stat_wfh SET wfh_end = ?, wfh_result = ? where wfh_id = ?", [new Date(), WorkflowsResultEnum::EXCEPTION(), $strWfRunId]);
                $objHandler->schedule();
                $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_SCHEDULED);
            }



            $objOneWorkflow->setIntRuns($objOneWorkflow->getIntRuns() + 1);
            $objOneWorkflow->updateObjectToDb();

            $objLockmanager->unlockRecord(true);

        }

        Logger::getInstance(self::STR_LOGFILE)->info("running workflows finished");
    }


}
