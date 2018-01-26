<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
********************************************************************************************************/

namespace Kajona\Workflows\Admin;

use Kajona\System\Admin\AdminEvensimpler;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\AdminInterface;
use Kajona\System\Admin\Formentries\FormentryDate;
use Kajona\System\Admin\Formentries\FormentryHidden;
use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\ArraySectionIterator;
use Kajona\System\System\Carrier;
use Kajona\System\System\Date;
use Kajona\System\System\Exception;
use Kajona\System\System\GraphFactory;
use Kajona\System\System\HttpStatuscodes;
use Kajona\System\System\Link;
use Kajona\System\System\Model;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\UserGroup;
use Kajona\System\System\UserUser;
use Kajona\Workflows\System\WorkflowsController;
use Kajona\Workflows\System\WorkflowsHandler;
use Kajona\Workflows\System\WorkflowsStats;
use Kajona\Workflows\System\WorkflowsWorkflow;

/**
 * Admin class of the workflows-module. Responsible for editing workflows and organizing them.
 *
 * @package module_workflows
 * @author sidler@mulchprod.de
 *
 * @module workflows
 * @moduleId _workflows_module_id_
 */
class WorkflowsAdmin extends AdminEvensimpler implements AdminInterface
{

    const STR_LIST_HANDLER = "STR_LIST_HANDLER";

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        //set default action
        if ($this->getParam("action") == "") {
            $this->setAction("myList");
        }
    }


    /**
     * @return array
     */
    public function getOutputModuleNavi()
    {
        $arrReturn = array();
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "myList", "", $this->getLang("module_mylist")));
        $arrReturn[] = array("edit", Link::getLinkAdmin($this->getArrModule("modul"), "list", "", $this->getLang("commons_list")));
        $arrReturn[] = array("", "");
        $arrReturn[] = array("right1", Link::getLinkAdmin($this->getArrModule("modul"), "listHandlers", "", $this->getLang("action_list_handlers")));
        $arrReturn[] = array("right2", Link::getLinkAdmin($this->getArrModule("modul"), "stats", "", $this->getLang("action_stats")));
        return $arrReturn;
    }

    /**
     * @return array
     */
    public function getArrOutputNaviEntries()
    {
        $arrPath = parent::getArrOutputNaviEntries();

        if (validateSystemid($this->getSystemid()) && Objectfactory::getInstance()->getObject($this->getSystemid()) != null) {
            $arrPath[] = Link::getLinkAdmin("workflows", $this->getAction(), "&systemid=".$this->getSystemid(), Objectfactory::getInstance()->getObject($this->getSystemid())->getStrDisplayName());
        }

        return $arrPath;
    }


    /**
     * Generates some stats about the current executions.
     *
     * @permissions right2
     */
    protected function actionStats()
    {

        if (validateSystemid($this->getSystemid())) {
            $this->setArrModuleEntry("template", "/folderview.tpl");

            $objStats = new WorkflowsStats();
            $arrTableRows = [];
            foreach ($objStats->getHandlerForController($this->getSystemid()) as $arrOneRow) {
                $arrTableRows[] = [
                    dateToString(new Date($arrOneRow["wfh_start"])),
                    $arrOneRow["wfh_end"] != "" ? dateToString(new Date($arrOneRow["wfh_end"])) : "<i class='fa fa-warning' style='color: red;'></i>",
                    $arrOneRow["wfh_end"] != "" ? round(((new Date($arrOneRow["wfh_end"]))->getTimeInOldStyle() - (new Date($arrOneRow["wfh_start"]))->getTimeInOldStyle()) / 60, 4) ." min"  : "",
                    $arrOneRow["wfh_result"],
                    $arrOneRow["wfh_class"],
                ];
            }

            return $this->objToolkit->dataTable([$this->getLang("stats_start"), $this->getLang("stats_end"), $this->getLang("stats_duration"),$this->getLang("stats_result"), $this->getLang("stats_class")], $arrTableRows);
        }


        $objDate = new Date();
        if ($this->getParam("stats_date") != "") {
            $objDate->generateDateFromParams("stats_date", Carrier::getAllParams());
        }

        $objForm = new AdminFormgenerator("stats", null);
        $objForm->addField(new FormentryDate("stats", "date"))->setStrValue($objDate);
        $strReturn = $objForm->renderForm(Link::getLinkAdminHref($this->getArrModule("module"), "stats", "&pv=1"));


        $objStats = new WorkflowsStats();

        //add a simple line-chart
        list($arrProcessedController, $arrBrokenController, $arrHandlers, $arrBrokenHandlers) = $objStats->getHourlyStats($objDate);

        $objChart = GraphFactory::getGraphInstance();

        $objChart->addLinePlot($arrProcessedController, $this->getLang("chart_processed"));
        $objChart->addLinePlot($arrBrokenController, $this->getLang("chart_broken"));
        $objChart->addLinePlot($arrHandlers, $this->getLang("chart_handler"));
        $objChart->addLinePlot($arrBrokenHandlers, $this->getLang("chart_handler_broken"));

        $objChart->setIntWidth(1200);
        $strReturn .= $objChart->renderGraph();



        $objIterator = new ArraySectionIterator($objStats->getControllerForDateCount($objDate));
        $objIterator->setPageNumber($this->getParam("pv"));
        $objIterator->setArraySection($objStats->getControllerForDate($objDate, $objIterator->calculateStartPos(), $objIterator->calculateEndPos()));


        $arrTableRows = [];
        foreach ($objIterator as $arrOneRow) {
            $arrTableRows[] = [
                dateToString(new Date($arrOneRow["wfc_start"])),
                $arrOneRow["wfc_end"] != "" ? dateToString(new Date($arrOneRow["wfc_end"])) : "<i class='fa fa-warning' style='color: red;'></i>",
                $arrOneRow["wfc_end"] != "" ? round(((new Date($arrOneRow["wfc_end"]))->getTimeInOldStyle() - (new Date($arrOneRow["wfc_start"]))->getTimeInOldStyle()) / 60, 4) ." min"  : "",
                $arrOneRow["anzhandler"],
                ($arrOneRow["anzexception"] > 0 ? "<i class='fa fa-warning' style='color: red;'></i> " : "").
                Link::getLinkAdminDialog($this->getArrModule("module"), $this->getAction(), ["systemid" => $arrOneRow["wfc_id"]], $this->getLang("action_show_details"), $this->getLang("action_show_details"), "icon_lens")
            ];
        }

        $strReturn .= $this->objToolkit->dataTable([$this->getLang("stats_start"), $this->getLang("stats_end"), $this->getLang("stats_duration"), $this->getLang("stats_amount"), ""], $arrTableRows);

        $strReturn .= $this->objToolkit->getPageview($objIterator, $this->getArrModule("module"), $this->getAction(), "&stats_date=".$objDate);
        return $strReturn;
    }


    /**
     * Renders the form to edit an existing entry
     *
     * @return string
     * @permissions edit
     */
    protected function actionEdit()
    {
        $objInstance = Objectfactory::getInstance()->getObject($this->getSystemid());

        if ($objInstance instanceof WorkflowsHandler && $objInstance->rightRight1()) {
            $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul"), "editHandler", "&systemid=".$objInstance->getSystemid()));
        }

        return "";
    }


    /**
     * Creates a list of all workflows-instances currently available.
     *
     * @return string
     * @autoTestable
     * @permissions view
     */
    protected function actionList()
    {
        $objIterator = new ArraySectionIterator(WorkflowsWorkflow::getObjectCountFiltered());
        $objIterator->setPageNumber($this->getParam("pv"));
        $objIterator->setArraySection(WorkflowsWorkflow::getAllworkflows($objIterator->calculateStartPos(), $objIterator->calculateEndPos()));

        return $this->renderList($objIterator);
    }


    /**
     * Creates a list of workflow-instances available for the current user
     *
     * @return string
     * @autoTestable
     * @permissions view
     */
    protected function actionMyList()
    {
        $objIterator = new ArraySectionIterator(
            WorkflowsWorkflow::getPendingWorkflowsForUserCount(array_merge(array($this->objSession->getUserID()), $this->objSession->getGroupIdsAsArray()))
        );
        $objIterator->setPageNumber($this->getParam("pv"));
        $objIterator->setArraySection(
            WorkflowsWorkflow::getPendingWorkflowsForUser(array_merge(array($this->objSession->getUserID()), $this->objSession->getGroupIdsAsArray()), $objIterator->calculateStartPos(), $objIterator->calculateEndPos())
        );

        return $this->renderList($objIterator);
    }


    /**
     * Shows technical details of a workflow-instance
     *
     * @return string
     * @permissions edit
     */
    protected function actionShowDetails()
    {
        $strReturn = "";
        $objWorkflow = new WorkflowsWorkflow($this->getSystemid());

        $strReturn .= $this->objToolkit->formHeadline($this->getLang("workflow_general"));

        $arrRows = array();
        $arrRows[] = array($this->getLang("workflow_class"), $objWorkflow->getStrClass());
        $arrRows[] = array($this->getLang("workflow_systemid"), $objWorkflow->getStrAffectedSystemid());
        $arrRows[] = array($this->getLang("workflow_trigger"), dateToString($objWorkflow->getObjTriggerdate()));
        $arrRows[] = array($this->getLang("workflow_runs"), $objWorkflow->getIntRuns());
        $arrRows[] = array($this->getLang("workflow_status"), $this->getLang("workflow_status_".$objWorkflow->getIntState()));

        $strResponsible = "";
        foreach (explode(",", $objWorkflow->getStrResponsible()) as $strOneId) {
            if (validateSystemid($strOneId)) {
                if ($strResponsible != "") {
                    $strResponsible .= ", ";
                }

                $objUser = new UserUser($strOneId, false);
                if ($objUser->getStrUsername() != "") {
                    $strResponsible .= $objUser->getStrUsername();
                } else {
                    $objGroup = new UserGroup($strOneId);
                    $strResponsible .= $objGroup->getStrName();
                }
            }
        }
        $arrRows[] = array($this->getLang("workflow_responsible"), $strResponsible);

        $strCreator = "";
        if (validateSystemid($objWorkflow->getStrOwner())) {
            $objUser = new UserUser($objWorkflow->getStrOwner(), false);
            $strCreator .= $objUser->getStrUsername();
        }
        $arrRows[] = array($this->getLang("workflow_owner"), $strCreator);
        $strReturn .= $this->objToolkit->dataTable(array(), $arrRows);


        $strReturn .= $this->objToolkit->formHeadline($this->getLang("workflow_params"));
        $arrRows = array();
        $arrRows[] = array($this->getLang("workflow_int1"), $objWorkflow->getIntInt1());
        $arrRows[] = array($this->getLang("workflow_int2"), $objWorkflow->getIntInt2());
        $arrRows[] = array($this->getLang("workflow_char1"), $objWorkflow->getStrChar1());
        $arrRows[] = array($this->getLang("workflow_char2"), $objWorkflow->getStrChar2());
        $arrRows[] = array($this->getLang("workflow_date1"), $objWorkflow->getLongDate1());
        $arrRows[] = array($this->getLang("workflow_date2"), $objWorkflow->getLongDate2());
        $arrRows[] = array($this->getLang("workflow_text"), $objWorkflow->getStrText());
        $arrRows[] = array($this->getLang("workflow_text2"), $objWorkflow->getStrText2());
        $arrRows[] = array($this->getLang("workflow_text3"), $objWorkflow->getStrText3());
        $strReturn .= $this->objToolkit->dataTable(array(), $arrRows);

        $strReturn .= $this->objToolkit->formHeader(Link::getLinkAdminHref($this->getArrModule("modul"), "list"));
        $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("commons_back"));
        $strReturn .= $this->objToolkit->formClose();

        return $strReturn;
    }

    /**
     * Creates the form to perform the current workflow-step
     *
     * @return string
     * @permissions view
     */
    protected function actionShowUi(AdminFormgenerator $objForm = null)
    {
        $strReturn = "";

        $objWorkflow = new WorkflowsWorkflow($this->getSystemid());
        if ($objWorkflow->getIntState() != WorkflowsWorkflow::$INT_STATE_SCHEDULED || !$objWorkflow->getObjWorkflowHandler()->providesUserInterface()) {
            return $this->getLang("commons_error_permissions");
        }

        $arrIdsToCheck = array_merge(array($this->objSession->getUserID()), $this->objSession->getGroupIdsAsArray());
        $arrIdsOfTask = explode(",", $objWorkflow->getStrResponsible());

        //ui given? current user responsible?
        //magic: the difference of the tasks' ids and the users' ids should be less than the count of the task-ids - then at least one id matches
        if ($objWorkflow->getObjWorkflowHandler()->providesUserInterface() && ($objWorkflow->getStrResponsible() == "" || count(array_diff($arrIdsOfTask, $arrIdsToCheck)) < count($arrIdsOfTask))) {

            $strReturn = $this->renderUserInputForm($objWorkflow, $objForm);
        }
        else {
            $strReturn = $this->getLang("commons_error_permissions");
        }

        return $strReturn;
    }

    /**
     * @param $objWorkflow
     * @param $strReturn
     * @return string
     * @throws Exception
     */
    protected function renderUserInputForm($objWorkflow, AdminFormgenerator $objForm = null)
    {
        $strReturn = "";

        $strCreator = "";
        if (validateSystemid($objWorkflow->getStrOwner())) {
            $objUser = new UserUser($objWorkflow->getStrOwner(), false);
            $strCreator .= $objUser->getStrUsername();
        }
        $strInfo = $this->objToolkit->getTextRow($this->getLang("workflow_owner") . " " . $strCreator);

        $strResponsible = "";
        foreach (explode(",", $objWorkflow->getStrResponsible()) as $strOneId) {
            if (validateSystemid($strOneId)) {
                if ($strResponsible != "") {
                    $strResponsible .= ", ";
                }

                $objUser = new UserUser($strOneId, false);
                if ($objUser->getStrUsername() != "") {
                    $strResponsible .= $objUser->getStrUsername();
                } else {
                    $objGroup = new UserGroup($strOneId);
                    $strResponsible .= $objGroup->getStrName();
                }
            }
        }

        $arrHeader = array($this->getLang("workflow_general"), "");
        $arrRow1 = array($this->getLang("workflow_owner"), $strCreator);
        $arrRow2 = array($this->getLang("workflow_responsible"), $strResponsible);
        $strReturn .= $this->objToolkit->dataTable($arrHeader, array($arrRow1, $arrRow2));

        $strForm = "";
        if ($objForm === null) {
            $strForm = $objWorkflow->getObjWorkflowHandler()->getUserInterface();
        }
        else {
            $strForm = $objForm;
        }

        if ($strForm instanceof AdminFormgenerator) {
            $strForm->addField(new FormentryHidden(null, "workflowid"))->setStrValue($objWorkflow->getSystemid());
            if ($strForm->getObjSourceobject() == null) {
                $strForm->addField(new FormentryHidden(null, "systemid"))->setStrValue($objWorkflow->getSystemid());
            }
            $strReturn .= $strForm->renderForm(Link::getLinkAdminHref($this->getArrModule("modul"), "saveUI"));
        } else {
            $strReturn .= $this->objToolkit->formHeader(Link::getLinkAdminHref($this->getArrModule("modul"), "saveUI"));
            $strReturn .= $strForm;
            $strReturn .= $this->objToolkit->formInputHidden("systemid", $objWorkflow->getSystemid());
            $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("commons_save"));
            $strReturn .= $this->objToolkit->formClose();
        }
        return $strReturn;
    }

    /**
     * Calls the handler to process the values collected by the ui before.
     *
     * @throws Exception
     * @return string
     * @permissions view
     */
    protected function actionSaveUi()
    {
        $strReturn = "";
        $objWorkflow = new WorkflowsWorkflow($this->getSystemid());

        $strForm = $objWorkflow->getObjWorkflowHandler()->getUserInterface();
        if ($strForm instanceof AdminFormgenerator) {
            if (!$strForm->validateForm()) {
                return $this->actionShowUi($strForm);
            }
        }

        $arrIdsToCheck = array_merge(array($this->objSession->getUserID()), $this->objSession->getGroupIdsAsArray());
        $arrIdsOfTask = explode(",", $objWorkflow->getStrResponsible());

        //ui given? current user responsible?
        //magic: the difference of the tasks' ids and the users' ids should be less than the count of the task-ids - then at least one id matches
        if ($objWorkflow->getObjWorkflowHandler()->providesUserInterface() && ($objWorkflow->getStrResponsible() == "" || count(array_diff($arrIdsOfTask, $arrIdsToCheck)) < count($arrIdsOfTask))) {
            $objHandler = $objWorkflow->getObjWorkflowHandler();
            $objHandler->processUserInput($this->getAllParams());

            if ($objWorkflow->getBitSaved() == true) {
                throw new Exception("Illegal state detected! Workflow was already saved before!", Exception::$level_FATALERROR);
            }

            $objWorkflow->updateObjectToDb();

            $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul"), "myList"));
        } else {
            $strReturn .= $this->getLang("commons_error_permissions");
        }

        return $strReturn;
    }

    /**
     * @param Model $objListEntry
     * @param bool $bitDialog
     *
     * @return string
     */
    protected function renderEditAction(Model $objListEntry, $bitDialog = false)
    {
        if ($objListEntry instanceof WorkflowsHandler) {
            return $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "editHandler", "&systemid=".$objListEntry->getSystemid(), "", $this->getLang("action_edit_handler"), "icon_edit"));
        } else {
            return "";
        }
    }


    /**
     * @param \Kajona\System\System\ModelInterface $objListEntry
     *
     * @return string
     */
    protected function renderUnlockAction(\Kajona\System\System\ModelInterface $objListEntry)
    {
        if ($objListEntry instanceof WorkflowsHandler) {
            return "";
        }
        return parent::renderUnlockAction($objListEntry);
    }

    /**
     * @param \Kajona\System\System\ModelInterface $objListEntry
     *
     * @return string
     */
    protected function renderDeleteAction(\Kajona\System\System\ModelInterface $objListEntry)
    {
        if ($objListEntry instanceof WorkflowsHandler) {
            return "";
        }
        return parent::renderDeleteAction($objListEntry);
    }

    /**
     * @param Model $objListEntry
     * @param string $strAltActive tooltip text for the icon if record is active
     * @param string $strAltInactive tooltip text for the icon if record is inactive
     *
     * @return string
     */
    protected function renderStatusAction(Model $objListEntry, $strAltActive = "", $strAltInactive = "")
    {
        if ($objListEntry instanceof WorkflowsHandler) {
            return "";
        }
        if ($objListEntry instanceof WorkflowsWorkflow) {
            $strStatusIcon = "";
            if ($objListEntry->getIntState() == WorkflowsWorkflow::$INT_STATE_NEW) {
                $strStatusIcon = AdminskinHelper::getAdminImage("icon_workflowNew", $this->getLang("workflow_status_".$objListEntry->getIntState()));
            }
            if ($objListEntry->getIntState() == WorkflowsWorkflow::$INT_STATE_SCHEDULED) {
                $strStatusIcon = AdminskinHelper::getAdminImage("icon_workflowScheduled", $this->getLang("workflow_status_".$objListEntry->getIntState()));
            }
            if ($objListEntry->getIntState() == WorkflowsWorkflow::$INT_STATE_EXECUTED) {
                $strStatusIcon = AdminskinHelper::getAdminImage("icon_workflowExecuted", $this->getLang("workflow_status_".$objListEntry->getIntState()));
            }

            if ($strStatusIcon != "") {
                return $this->objToolkit->listButton($strStatusIcon);
            }
        }
        return parent::renderStatusAction($objListEntry, $strAltActive, $strAltInactive);
    }

    /**
     * @param Model $objListEntry
     *
     * @return string
     */
    protected function renderCopyAction(Model $objListEntry)
    {
        return "";
    }

    /**
     * @param Model $objListEntry
     *
     * @return array
     */
    protected function renderAdditionalActions(Model $objListEntry)
    {
        if ($objListEntry instanceof WorkflowsHandler) {
            return array(
                $this->objToolkit->listConfirmationButton(
                    $this->getLang("handler_instatiate", array($objListEntry->getStrDisplayName())),
                    Link::getLinkAdminHref($this->getArrModule("modul"), "startInstance", "&systemid=".$objListEntry->getSystemid()),
                    "icon_workflowTrigger",
                    $this->getLang("action_instantiate_handler")
                )
            );
        }
        if ($objListEntry instanceof WorkflowsWorkflow) {
            $arrReturn = array();
            if ($objListEntry->getIntState() == WorkflowsWorkflow::$INT_STATE_SCHEDULED && $objListEntry->getObjWorkflowHandler()->providesUserInterface()) {
                $arrReturn[] = $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "showUI", "&systemid=".$objListEntry->getSystemid(), "", $this->getLang("workflow_ui"), "icon_workflow_ui"));
            }

            if ($objListEntry->rightEdit()) {
                $arrReturn[] = $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "showDetails", "&systemid=".$objListEntry->getSystemid(), "", $this->getLang("action_show_details"), "icon_lens"));
            }

            return $arrReturn;
        }
        return parent::renderAdditionalActions($objListEntry);
    }

    /**
     * @param string $strListIdentifier
     * @param bool $bitDialog
     *
     * @return string
     */
    protected function getNewEntryAction($strListIdentifier, $bitDialog = false)
    {
        return "";
    }

    /**
     * Lists all handlers available to the system
     *
     * @return string
     * @autoTestable
     * @permissions right1
     */
    protected function actionListHandlers()
    {
        WorkflowsHandler::synchronizeHandlerList();

        $strReturn = $this->objToolkit->formHeadline($this->getLang("action_list_handlers"));

        $objIterator = new ArraySectionIterator(WorkflowsHandler::getObjectCountFiltered());
        $objIterator->setPageNumber($this->getParam("pv"));
        $objIterator->setArraySection(WorkflowsHandler::getObjectListFiltered(null, "", $objIterator->calculateStartPos(), $objIterator->calculateEndPos()));

        $strReturn .= $this->renderList($objIterator, false, self::STR_LIST_HANDLER);
        return $strReturn;
    }

    /**
     * Renders the form to edit a workflow-handlers default values
     *
     * @param AdminFormgenerator $objForm
     *
     * @return string
     * @permissions right1
     */
    protected function actionEditHandler(AdminFormgenerator $objForm = null)
    {
        $strReturn = "";
        $objHandler = new WorkflowsHandler($this->getSystemid());
        //check rights
        if ($objHandler->rightEdit()) {
            if ($objForm == null) {
                $objForm = $this->getHandlerForm($objHandler);
            }


            $strReturn .= $this->objToolkit->formHeadline($objHandler->getObjInstanceOfHandler()->getStrName()." (".$objHandler->getStrHandlerClass().")");
            $strReturn .= $objForm->renderForm(getLinkAdminHref($this->getArrModule("modul"), "saveHandler"));
            return $strReturn;
        } else {
            $strReturn .= $this->getLang("commons_error_permissions");
        }

        return $strReturn;
    }


    /**
     * @param WorkflowsHandler $objHandler
     *
     * @return AdminFormgenerator
     */
    private function getHandlerForm(WorkflowsHandler $objHandler)
    {
        $objForm = new AdminFormgenerator("handler", $objHandler);
        $objForm->generateFieldsFromObject();

        $arrNames = $objHandler->getObjInstanceOfHandler()->getConfigValueNames();

        $objForm->getField("configval1")->setStrLabel((isset($arrNames[0]) ? $arrNames[0] : $this->getLang("workflow_handler_val1")));
        $objForm->getField("configval2")->setStrLabel((isset($arrNames[1]) ? $arrNames[1] : $this->getLang("workflow_handler_val2")));
        $objForm->getField("configval3")->setStrLabel((isset($arrNames[2]) ? $arrNames[2] : $this->getLang("workflow_handler_val3")));

        return $objForm;
    }

    /**
     * @return string
     * @permissions right1
     */
    protected function actionSaveHandler()
    {

        $objHandler = new WorkflowsHandler($this->getSystemid());

        if ($objHandler->rightRight1()) {
            $objForm = $this->getHandlerForm($objHandler);
            if (!$objForm->validateForm()) {
                return $this->actionEditHandler($objForm);
            }

            $objForm->updateSourceObject();
            $objHandler->updateObjectToDb();

            $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "listHandlers", ""));
            return "";
        } else {
            return $this->getLang("commons_error_permissions");
        }
    }


    /**
     * @return string
     * @permissions right1
     */
    protected function actionStartInstance()
    {
        $strReturn = "";

        $objHandler = new WorkflowsHandler($this->getSystemid());
        $objWorkflow = new WorkflowsWorkflow();
        $objWorkflow->setStrClass($objHandler->getStrHandlerClass());
        $objWorkflow->updateObjectToDb();
        $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "list"));

        return $strReturn;
    }

    /**
     * Triggers the workflow engine
     *
     * @return string
     * @permissions anonymous
     */
    protected function actionTrigger()
    {
        Carrier::getInstance()->getObjSession()->setBitBlockDbUpdate(true);
        if ($this->getParam("authkey") == SystemSetting::getConfigValue("_workflows_trigger_authkey_")) {
            $objWorkflowController = new WorkflowsController();
            $objWorkflowController->processWorkflows();
            return "<message>Execution successful</message>";
        }


        ResponseObject::getInstance()->setStrStatusCode(HttpStatuscodes::SC_UNAUTHORIZED);
        return "<message><error>Not authorized</error></message>";
    }

}

