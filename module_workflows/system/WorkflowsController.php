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


    /**
     * Searches for new workflows and forces them to schedule and initialize
     */
    public function scheduleWorkflows()
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
    public function runWorkflows()
    {
        $arrWorkflows = WorkflowsWorkflow::getWorkflowsByType(WorkflowsWorkflow::$INT_STATE_SCHEDULED);

        Logger::getInstance(self::STR_LOGFILE)->info("running workflows, count: ".count($arrWorkflows));


        if (!defined("_workflow_is_running_")) {
            define("_workflow_is_running_", true);
        }

        foreach ($arrWorkflows as $objOneWorkflow) {

            if ($objOneWorkflow->getIntRecordStatus() == 0) {
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." is inactive, can't be executed");
                continue;
            }

            //lock the workflow
            $objLockmanager = $objOneWorkflow->getLockManager();
            if ($objLockmanager->isLocked()) {
                Logger::getInstance(self::STR_LOGFILE)->warning("workflow ".$objOneWorkflow->getSystemid()." is locked, can't be executed");
                continue;
            }

            //double-check if the workflow is still pending. it's possible that the workflow was fetched in the meantime by another thread.
            //so skip if the wf-state is either no longer scheduled or the nr of executions differ
            $arrRow = Carrier::getInstance()->getObjDB()->getPRow("SELECT * FROM "._dbprefix_."workflows WHERE workflows_id = ?", array($objOneWorkflow->getSystemid()), 0, false);
            if($arrRow["workflows_state"] != WorkflowsWorkflow::$INT_STATE_SCHEDULED || $arrRow["workflows_runs"] != $objOneWorkflow->getIntRuns()) {
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
            if ($objHandler->execute()) {
                //handler executed successfully. shift to state 'executed'
                $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_EXECUTED);
                Logger::getInstance(self::STR_LOGFILE)->info(" execution finished, new state: executed");
            }
            else {
                //handler failed to execute. reschedule.
                $objHandler->schedule();
                $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_SCHEDULED);
                Logger::getInstance(self::STR_LOGFILE)->info(" execution finished, new state: scheduled");
            }

            $objOneWorkflow->setIntRuns($objOneWorkflow->getIntRuns() + 1);
            $objOneWorkflow->updateObjectToDb();

            $objLockmanager->unlockRecord(true);

        }

        Logger::getInstance(self::STR_LOGFILE)->info("running workflows finished");
    }

    /**
     * Runs a single workflow.
     *
     * @param WorkflowsWorkflow $objOneWorkflow
     *
     * @deprecated
     */
    public function runSingleWorkflow($objOneWorkflow)
    {

        if (!defined("_workflow_is_running_")) {
            define("_workflow_is_running_", true);
        }

        $objHandler = $objOneWorkflow->getObjWorkflowHandler();

        if ($objOneWorkflow->getIntState() != WorkflowsWorkflow::$INT_STATE_SCHEDULED || $objOneWorkflow->getIntRecordStatus() == 0) {
            return;
        }

        //trigger the workflow
        Logger::getInstance(self::STR_LOGFILE)->info("executing workflow ".$objOneWorkflow->getSystemid());
        if ($objHandler->execute()) {
            //handler executed successfully. shift to state 'executed'
            $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_EXECUTED);
            Logger::getInstance(self::STR_LOGFILE)->info(" execution finished, new state: executed");
        }
        else {
            //handler failed to execute. reschedule.
            $objHandler->schedule();
            $objOneWorkflow->setIntState(WorkflowsWorkflow::$INT_STATE_SCHEDULED);
            Logger::getInstance(self::STR_LOGFILE)->info(" execution finished, new state: scheduled");
        }

        $objOneWorkflow->setIntRuns($objOneWorkflow->getIntRuns() + 1);
        $objOneWorkflow->updateObjectToDb();

    }

}
