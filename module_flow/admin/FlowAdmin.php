<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\Flow\Admin;

use Kajona\Flow\System\FlowActionAbstract;
use Kajona\Flow\System\FlowActionUserInputInterface;
use Kajona\Flow\System\FlowConditionAbstract;
use Kajona\Flow\System\FlowConfig;
use Kajona\Flow\System\FlowGraphWriter;
use Kajona\Flow\System\FlowManager;
use Kajona\Flow\System\FlowStatus;
use Kajona\Flow\System\FlowStatusFilter;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\Admin\AdminEvensimpler;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\AdminInterface;
use Kajona\System\Admin\Formentries\FormentryHeadline;
use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\ArraySectionIterator;
use Kajona\System\System\Lang;
use Kajona\System\System\Link;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\Objectfactory;

/**
 * Admin class to setup status transition flows
 *
 * @author christoph.kappestein@gmail.com
 *
 * @objectList Kajona\Flow\System\FlowConfig
 *
 * @objectListStep Kajona\Flow\System\FlowStatus
 * @objectNewStep Kajona\Flow\System\FlowStatus
 * @objectEditStep Kajona\Flow\System\FlowStatus
 * @objectFilterStep Kajona\Flow\System\FlowStatusFilter
 *
 * @objectListTransition Kajona\Flow\System\FlowTransition
 * @objectNewTransition Kajona\Flow\System\FlowTransition
 * @objectEditTransition Kajona\Flow\System\FlowTransition
 *
 * @objectListTransitionAction Kajona\Flow\System\FlowActionAbstract
 * @objectNewTransitionAction Kajona\Flow\System\FlowActionAbstract
 * @objectEditTransitionAction Kajona\Flow\System\FlowActionAbstract
 *
 * @objectListTransitionCondition Kajona\Flow\System\FlowConditionAbstract
 * @objectNewTransitionCondition Kajona\Flow\System\FlowConditionAbstract
 * @objectEditTransitionCondition Kajona\Flow\System\FlowConditionAbstract
 *
 * @module flow
 * @moduleId _flow_module_id_
 */
class FlowAdmin extends AdminEvensimpler implements AdminInterface
{
    /**
     * @inject flow_manager
     * @var FlowManager
     */
    protected $objFlowManager;

    /**
     * @return array
     */
    public function getOutputModuleNavi()
    {
        $arrReturn = array();
        $arrReturn[] = array("view", Link::getLinkAdmin($this->getArrModule("modul"), "list", "", $this->getLang("list_flow"), "", "", true, "adminnavi"));
        return $arrReturn;
    }

    public function renderAdditionalActions(Model $objListEntry)
    {
        $arrActions = parent::renderAdditionalActions($objListEntry);

        if ($objListEntry instanceof FlowConfig) {
            $arrActions[] = $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "listStep", "&systemid=" . $objListEntry->getSystemid(), "", $this->getLang("action_steps"), "icon_kriterium"));
        } elseif ($objListEntry instanceof FlowStatus) {
            $arrActions[] = $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "listTransition", "&systemid=" . $objListEntry->getSystemid(), "", $this->getLang("action_transitions"), "icon_project"));
        } elseif ($objListEntry instanceof FlowTransition) {
            $arrActions[] = $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "listTransitionAction", "&systemid=" . $objListEntry->getSystemid(), "", $this->getLang("action_transition_action"), "icon_play"));
            $arrActions[] = $this->objToolkit->listButton(Link::getLinkAdmin($this->getArrModule("modul"), "listTransitionCondition", "&systemid=" . $objListEntry->getSystemid(), "", $this->getLang("action_transition_condition"), "icon_table"));
        }

        return $arrActions;
    }

    public function renderTagAction(Model $objListEntry)
    {
        if ($objListEntry instanceof FlowConfig) {
            return "";
        } elseif ($objListEntry instanceof FlowStatus) {
            return "";
        } elseif ($objListEntry instanceof FlowTransition) {
            return "";
        } elseif ($objListEntry instanceof FlowActionAbstract) {
            return "";
        } elseif ($objListEntry instanceof FlowConditionAbstract) {
            return "";
        }

        return parent::renderTagAction($objListEntry);
    }

    public function renderPermissionsAction(Model $objListEntry)
    {
        if ($objListEntry instanceof FlowConfig) {
            return "";
        } elseif ($objListEntry instanceof FlowStatus) {
            return "";
        } elseif ($objListEntry instanceof FlowTransition) {
            return "";
        } elseif ($objListEntry instanceof FlowActionAbstract) {
            return "";
        } elseif ($objListEntry instanceof FlowConditionAbstract) {
            return "";
        }

        return parent::renderPermissionsAction($objListEntry);
    }

    public function renderStatusAction(Model $objListEntry, $strAltActive = "", $strAltInactive = "")
    {
        if ($objListEntry instanceof FlowConfig) {
            return $this->objToolkit->listStatusButton($objListEntry, true, $strAltActive, $strAltInactive);
        } elseif ($objListEntry instanceof FlowStatus) {
            return "";
        } elseif ($objListEntry instanceof FlowTransition) {
            return "";
        } elseif ($objListEntry instanceof FlowActionAbstract) {
            return "";
        } elseif ($objListEntry instanceof FlowConditionAbstract) {
            return "";
        }

        return parent::renderStatusAction($objListEntry, $strAltActive, $strAltInactive);
    }

    public function renderCopyAction(Model $objListEntry)
    {
        if ($objListEntry instanceof FlowConfig) {
        } elseif ($objListEntry instanceof FlowStatus) {
            return "";
        } elseif ($objListEntry instanceof FlowTransition && $objListEntry->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowActionAbstract) {
            return "";
        } elseif ($objListEntry instanceof FlowConditionAbstract) {
            return "";
        }

        return parent::renderCopyAction($objListEntry);
    }

    protected function renderEditAction(Model $objListEntry, $bitDialog = false)
    {
        if ($objListEntry instanceof FlowConfig) {
            return "";
        } elseif ($objListEntry instanceof FlowStatus && $objListEntry->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowTransition && $objListEntry->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowActionAbstract && $objListEntry->getTransition()->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowConditionAbstract && $objListEntry->getTransition()->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        }

        return parent::renderEditAction($objListEntry, $bitDialog);
    }

    protected function renderDeleteAction(ModelInterface $objListEntry)
    {
        if ($objListEntry instanceof FlowConfig) {
            if ($objListEntry->getIntRecordStatus() === 0) {
                return parent::renderDeleteAction($objListEntry);
            } else {
                return "";
            }
        } elseif ($objListEntry instanceof FlowStatus && $objListEntry->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowTransition && $objListEntry->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowActionAbstract && $objListEntry->getTransition()->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        } elseif ($objListEntry instanceof FlowConditionAbstract && $objListEntry->getTransition()->getParentStatus()->getFlowConfig()->getIntRecordStatus() === 1) {
            return "";
        }

        if ($objListEntry instanceof FlowStatus && ($objListEntry->getIntIndex() == FlowConfig::STATUS_START || $objListEntry->getIntIndex() == FlowConfig::STATUS_END)) {
            return $this->objToolkit->listButton(AdminskinHelper::getAdminImage("icon_deleteDisabled", $this->getLang("flow_step_no_delete")));
        }

        return parent::renderDeleteAction($objListEntry);
    }

    protected function getOutputNaviEntry(ModelInterface $objInstance)
    {
        if ($objInstance instanceof FlowConfig) {
            return Link::getLinkAdmin("flow", "listStep", "&systemid=" . $objInstance->getSystemid(), $objInstance->getStrDisplayName());
        } elseif ($objInstance instanceof FlowStatus) {
            return Link::getLinkAdmin("flow", "listTransition", "&systemid=" . $objInstance->getSystemid(), $objInstance->getStrDisplayName());
        } elseif ($objInstance instanceof FlowTransition) {
            return Link::getLinkAdmin("flow", "listTransition", "&systemid=" . $objInstance->getPrevId(), $objInstance->getStrDisplayName());
        } elseif ($objInstance instanceof FlowActionAbstract) {
            return Link::getLinkAdmin("flow", "listTransitionAction", "&systemid=" . $objInstance->getPrevId(), $objInstance->getStrDisplayName());
        } elseif ($objInstance instanceof FlowConditionAbstract) {
            return Link::getLinkAdmin("flow", "listTransitionCondition", "&systemid=" . $objInstance->getPrevId(), $objInstance->getStrDisplayName());
        } else {
            return null;
        }
    }

    public function getNewEntryAction($strListIdentifier, $bitDialog = false)
    {
        $strAction = parent::getNewEntryAction($strListIdentifier, $bitDialog);
        $strSystemId = $this->getParam("systemid");
        $objFlow = $this->getFlowFromSystemId($strSystemId);

        // if the flow is release we cant add new entries
        if ($objFlow !== null && $objFlow->getIntRecordStatus() === 1) {
            return [];
        }

        if ($strListIdentifier == "list") {
            return [];
        } elseif ($strListIdentifier == "listStep") {
            return $this->objToolkit->listButton(
                Link::getLinkAdmin(
                    $this->getArrModule("modul"), "newStep", "&systemid=".$strSystemId,
                    $this->getLang("commons_list_new"), $this->getLang("commons_list_new"), "icon_new"
                )
            );
        } elseif ($strListIdentifier == "listFlow") {
            return [];
        } elseif ($strListIdentifier == "listTransitionAction") {
            $arrLinks = array();
            $arrActions = $objFlow->getHandler()->getAvailableActions();
            foreach ($arrActions as $strActionClass) {
                $objAction = new $strActionClass();
                $arrLinks[] = $this->objToolkit->listButton(
                    Link::getLinkAdmin(
                        $this->getArrModule("modul"), $this->getActionNameForClass("newTransitionAction", null), "&systemid=".$this->getSystemid()."&class=".$strActionClass.$this->getStrPeAddon(), $objAction->getTitle(), $objAction->getTitle(), "icon_textfield", $objAction->getTitle()
                    )
                );
            }

            return $arrLinks;
        } elseif ($strListIdentifier == "listTransitionCondition") {
            $arrLinks = array();
            $arrActions = $objFlow->getHandler()->getAvailableConditions();
            foreach ($arrActions as $strConditionClass) {
                $objCondition = new $strConditionClass();
                $arrLinks[] = $this->objToolkit->listButton(
                    Link::getLinkAdmin(
                        $this->getArrModule("modul"), $this->getActionNameForClass("newTransitionCondition", null), "&systemid=".$this->getSystemid()."&class=".$strConditionClass.$this->getStrPeAddon(), $objCondition->getTitle(), $objCondition->getTitle(), "icon_textfield", $objCondition->getTitle()
                    )
                );
            }

            return $arrLinks;
        } else {
            return $strAction;
        }
    }

    /**
     * @return string
     * @permissions view
     */
    public function actionListStep()
    {
        $this->setStrCurObjectTypeName('Step');
        $this->setCurObjectClassName(FlowStatus::class);

        /** @var FlowConfig $objFlow */
        $objFlow = $this->objFactory->getObject($this->getParam("systemid"));

        /* Create list */
        $objFilter = FlowStatusFilter::getOrCreateFromSession();
        $objArraySectionIterator = new ArraySectionIterator(FlowStatus::getObjectCountFiltered($objFilter, $this->getSystemid()));
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(FlowStatus::getObjectListFiltered($objFilter, $this->getSystemid(), $objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos()));

        /* Render list and filter */
        $strList = "";
        $strList.= $this->objToolkit->formHeader("#");
        $strList.= $this->objToolkit->formInputText("set[".$objFlow->getSystemid()."]", $this->getLang("form_flow_name"), $objFlow->getStrName(), "", "", false, $objFlow->getSystemid()."#strName");
        $strList.= $this->objToolkit->formClose();
        $strList.= $this->renderList($objArraySectionIterator, true, "list".$this->getStrCurObjectTypeName());
        $strList.= "<script type='text/javascript'>require(['instantSave'], function(is) {is.init()});</script>";

        $strGraph = FlowGraphWriter::write($objFlow);

        $strHtml = "<div class='row'>";
        $strHtml .= "<div class='col-md-6'>" . $strList . "</div>";
        $strHtml .= "<div class='col-md-6'>" . $strGraph . "</div>";
        $strHtml .= "</div>";

        return $strHtml;
    }

    /**
     * @return string
     * @permissions view
     */
    public function actionListTransition()
    {
        $this->setStrCurObjectTypeName('Transition');
        $this->setCurObjectClassName(FlowTransition::class);

        /** @var FlowStatus $objStatus */
        $objStatus = $this->objFactory->getObject($this->getParam("systemid"));

        /* Create list */
        $objFilter = null;
        $objArraySectionIterator = new ArraySectionIterator(FlowTransition::getObjectCountFiltered($objFilter, $this->getSystemid()));
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(FlowTransition::getObjectListFiltered($objFilter, $this->getSystemid(), $objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos()));

        /* Render list and filter */
        $strList = $this->renderList($objArraySectionIterator, true, "list".$this->getStrCurObjectTypeName());
        $strGraph = FlowGraphWriter::write($objStatus->getFlowConfig(), $objStatus);

        $strHtml = "<div class='row'>";
        $strHtml .= "<div class='col-md-6'>" . $strList . "</div>";
        $strHtml .= "<div class='col-md-6'>" . $strGraph . "</div>";
        $strHtml .= "</div>";

        return $strHtml;
    }

    /**
     * @return string
     * @permissions view
     */
    public function actionListTransitionAction()
    {
        $this->setStrCurObjectTypeName('TransitionAction');
        $this->setCurObjectClassName(FlowActionAbstract::class);

        /** @var FlowTransition $objTransition */
        $objTransition = $this->objFactory->getObject($this->getParam("systemid"));

        /* Create list */
        $objFilter = null;
        $objArraySectionIterator = new ArraySectionIterator(FlowActionAbstract::getObjectCountFiltered($objFilter, $this->getSystemid()));
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(FlowActionAbstract::getObjectListFiltered($objFilter, $this->getSystemid(), $objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos()));

        /* Render list and filter */
        $strList = $this->objToolkit->warningBox($this->getLang("flow_transition_action_list", [$objTransition->getParentStatus()->getStrName(), $objTransition->getTargetStatus()->getStrName()]), "alert-info");
        $strList .= $this->renderList($objArraySectionIterator, true, "list".$this->getStrCurObjectTypeName());
        $strGraph = FlowGraphWriter::write($objTransition->getParentStatus()->getFlowConfig(), $objTransition);

        $strHtml = "<div class='row'>";
        $strHtml .= "<div class='col-md-6'>" . $strList . "</div>";
        $strHtml .= "<div class='col-md-6'>" . $strGraph . "</div>";
        $strHtml .= "</div>";

        return $strHtml;
    }

    /**
     * @return string
     * @permissions view
     */
    public function actionListTransitionCondition()
    {
        $this->setStrCurObjectTypeName('TransitionCondition');
        $this->setCurObjectClassName(FlowConditionAbstract::class);

        /** @var FlowTransition $objTransition */
        $objTransition = $this->objFactory->getObject($this->getParam("systemid"));

        /* Create list */
        $objFilter = null;
        $objArraySectionIterator = new ArraySectionIterator(FlowConditionAbstract::getObjectCountFiltered($objFilter, $this->getSystemid()));
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(FlowConditionAbstract::getObjectListFiltered($objFilter, $this->getSystemid(), $objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos()));

        /* Render list and filter */
        $strList = $this->objToolkit->warningBox($this->getLang("flow_transition_condition_list", [$objTransition->getParentStatus()->getStrName(), $objTransition->getTargetStatus()->getStrName()]), "alert-info");
        $strList .= $this->renderList($objArraySectionIterator, true, "list".$this->getStrCurObjectTypeName());
        $strGraph = FlowGraphWriter::write($objTransition->getParentStatus()->getFlowConfig(), $objTransition);

        $strHtml = "<div class='row'>";
        $strHtml .= "<div class='col-md-6'>" . $strList . "</div>";
        $strHtml .= "<div class='col-md-6'>" . $strGraph . "</div>";
        $strHtml .= "</div>";

        return $strHtml;
    }

    /**
     * @return string
     * @permissions view
     */
    protected function actionStepBrowser()
    {
        $this->setArrModuleEntry("template", "/folderview.tpl");

        $strReturn = "";
        $strFormElement = $this->getParam("form_element");

        $objStatus = $this->objFactory->getObject($this->getSystemid());
        $arrStatus = array();

        if ($objStatus instanceof FlowConfig) {
            $arrStatus = $objStatus->getArrStatus();
        } elseif ($objStatus instanceof FlowStatus) {
            /** @var FlowConfig $objFlow */
            $objFlow = $this->objFactory->getObject($objStatus->getPrevId());
            $arrStatus = $objFlow->getArrStatus();
        }

        $strReturn .= $this->objToolkit->listHeader();
        foreach ($arrStatus as $objStatus) {
            if (!$objStatus->rightView()) {
                continue;
            }

            $strAction = "";
            $strAction .= $this->objToolkit->listButton(
                "<a href=\"#\" title=\"".$this->getLang("prozess_uebernehmen")."\" rel=\"tooltip\" onClick=\"require('v4skin').setObjectListItems(
                    '".$strFormElement."', [{strSystemId: '".$objStatus->getSystemid()."', strDisplayName: '".addslashes($objStatus->getStrName())."', strIcon:'".addslashes(getImageAdmin("icon_treeLeaf", "", true))."'}], null, '".addslashes(getImageAdmin("icon_delete", "", true))."'); parent.KAJONA.admin.folderview.dialog.hide();\">"
                .AdminskinHelper::getAdminImage("icon_accept") . "</a>"
            );

            $strReturn .= $this->objToolkit->simpleAdminList($objStatus, $strAction);
        }

        $strReturn .= $this->objToolkit->listFooter();

        if (count($arrStatus) == 0) {
            $strReturn .= $this->getLang("commons_list_empty");
        }

        return $strReturn;
    }

    /**
     * @return string
     * @permissions edit
     */
    public function actionNewTransitionAction()
    {
        $this->setCurObjectClassName($this->getParam("class"));
        $this->setStrCurObjectTypeName("TransitionAction");
        return parent::actionNew();
    }

    /**
     * @return string
     * @permissions edit
     */
    public function actionEditTransitionAction()
    {
        $objAction = Objectfactory::getInstance()->getObject($this->getSystemid());
        $this->setCurObjectClassName($objAction->getStrRecordClass());
        $this->setStrCurObjectTypeName("TransitionAction");
        return parent::actionEdit();
    }

    /**
     * @return string
     * @permissions edit
     */
    public function actionSaveTransitionAction()
    {
        $this->setCurObjectClassName($this->getParam("class"));
        $this->setStrCurObjectTypeName("TransitionAction");
        return parent::actionSave();
    }

    /**
     * @return string
     * @permissions edit
     */
    public function actionNewTransitionCondition()
    {
        $this->setCurObjectClassName($this->getParam("class"));
        $this->setStrCurObjectTypeName("TransitionCondition");
        return parent::actionNew();
    }

    /**
     * @return string
     * @permissions edit
     */
    public function actionEditTransitionCondition()
    {
        $objAction = Objectfactory::getInstance()->getObject($this->getSystemid());
        $this->setCurObjectClassName($objAction->getStrRecordClass());
        $this->setStrCurObjectTypeName("TransitionCondition");
        return parent::actionEdit();
    }

    /**
     * @return string
     * @permissions edit
     */
    public function actionSaveTransitionCondition()
    {
        $this->setCurObjectClassName($this->getParam("class"));
        $this->setStrCurObjectTypeName("TransitionCondition");
        return parent::actionSave();
    }

    /**
     * @return string
     * @permissions view
     */
    public function actionSetNextStatus()
    {
        $strSystemId = $this->getSystemid();
        $strRedirect = $this->getParam("redirect");

        if (!validateSystemid($strSystemId)) {
            throw new \RuntimeException("No systemid provided");
        }

        $objObject = Objectfactory::getInstance()->getObject($strSystemId);

        if (!$objObject->rightEdit()) {
            throw new \RuntimeException("No rights to edit the object");
        }

        /** @var FlowConfig $objFlow */
        $objFlow = $this->objFlowManager->getFlowForModel($objObject);

        if (!$objFlow instanceof FlowConfig) {
            throw new \RuntimeException("No flow is assigned to the provided object");
        }

        $arrTransitions = $this->objFlowManager->getPossibleTransitionsForModel($objObject);
        if (!empty($arrTransitions)) {
            $strHtml = "";
            $strHtml.= $this->objToolkit->formHeader(Link::getLinkAdminHref("flow", "setNextStatusSave"));
            $strHtml.= $this->objToolkit->getTextRow($this->objToolkit->warningBox($this->getLang("form_next_status_description", "flow", [$objObject->getStrDisplayName()]), "alert-info"));
            $strHtml.= $this->objToolkit->formInputHidden("systemid", $strSystemId);
            $strHtml.= $this->objToolkit->formInputHidden("redirect", $strRedirect);

            $arrOptions = [];
            $strValidation = "";

            // @TODO if we have only one possible status we could directly set the status of the record

            foreach ($arrTransitions as $objTransition) {
                /** @var FlowTransition $objTransition */
                $objTargetStatus = $objTransition->getTargetStatus();

                // validation
                $objResult = $objFlow->getHandler()->validateStatusTransition($objObject, $objTransition);

                if (!$objResult->isValid()) {
                    $arrErrors = $objResult->getErrors();
                    if (!empty($arrErrors)) {
                        $strValidation .= "<div id='error-" . $objTransition->getSystemid() . "' class='transition-error alert alert-danger' style='display:none'>";
                        $strValidation .= "<ul>";
                        foreach ($arrErrors as $strError) {
                            if (!empty($strError)) {
                                $strError = htmlspecialchars($strError);
                                $strValidation .= "<li>{$strError}</li>";
                            }
                        }
                        $strValidation .= "</ul>";
                        $strValidation .= "</div>";
                    } else {
                        // in case the result is not valid and we have no error message we skip the menu entry
                        continue;
                    }
                }

                $arrOptions[$objTransition->getSystemid()] = $objTargetStatus->getStrDisplayName();
            }

            $strHtml.= $this->objToolkit->formInputDropdown("transition_id", $arrOptions, "Status", key($arrOptions));
            $strHtml.= $this->objToolkit->formTextRow($strValidation);

            $strButtons = "";
            $strButtons.= $this->objToolkit->formInputSubmit(Lang::getInstance()->getLang("commons_cancel", "system"), "cancelbtn", "", "", true, false);
            $strButtons.= $this->objToolkit->formInputSubmit(Lang::getInstance()->getLang("commons_submit", "system"), "submitbtn", "", "", true, false);

            $strHtml.= $this->objToolkit->formInputButtonWrapper($strButtons);
            $strHtml.= $this->objToolkit->formClose();

            $strHtml.= <<<HTML
<script type="text/javascript">
$('#transition_id').on('click', function(){
    $('.transition-error').hide();
    $('#error-' + $(this).val()).show();
});
</script> 
HTML;

            return $strHtml;
        } else {
            // no status possible so redirect
            $this->adminReload($strRedirect);
            return "";
        }
    }

    /**
     * @return string
     * @permissions view
     */
    public function actionSetNextStatusSave()
    {
        $strSystemId = $this->getSystemid();
        $strRedirect = $this->getParam("redirect");
        $strTransitionId = $this->getParam("transition_id");

        if (!validateSystemid($strSystemId)) {
            throw new \RuntimeException("No systemid provided");
        }

        $objObject = Objectfactory::getInstance()->getObject($strSystemId);

        if (!$objObject->rightEdit()) {
            throw new \RuntimeException("No rights to edit the object");
        }

        /** @var FlowConfig $objFlow */
        $objFlow = $this->objFlowManager->getFlowForModel($objObject);

        if (!$objFlow instanceof FlowConfig) {
            throw new \RuntimeException("No flow is assigned to the provided object");
        }

        $objTransition = Objectfactory::getInstance()->getObject($strTransitionId);
        if ($objTransition instanceof FlowTransition) {
            $arrActions = $objTransition->getArrActions();
            $objForm = new AdminFormgenerator("", null);
            $bitInputRequired = false;

            foreach ($arrActions as $objAction) {
                if ($objAction instanceof FlowActionUserInputInterface) {
                    $objForm->addField(new FormentryHeadline())->setStrValue($objAction->getTitle());
                    $objAction->configureUserInputForm($objForm);
                    $bitInputRequired = true;
                }
            }

            if ($bitInputRequired) {
                if ($_SERVER["REQUEST_METHOD"] == "GET" || !$objForm->validateForm()) {
                    $strForm = $objForm->renderForm(Link::getLinkAdminHref($this->getArrModule("modul"), "setNextStatusSave", "&systemid=" . $objObject->getStrSystemid() . "&transition_id=" . $strTransitionId));
                    return $strForm;
                } else {
                    foreach ($arrActions as $objAction) {
                        if ($objAction instanceof FlowActionUserInputInterface) {
                            $objActionForm = new AdminFormgenerator("", null);
                            $objAction->configureUserInputForm($objActionForm);
                            $arrFields = $objActionForm->getArrFields();

                            $arrData = [];
                            foreach ($arrFields as $strName => $objField) {
                                $arrData[$strName] = $this->getParam($strName);
                            }

                            $objAction->handleUserInput($objObject, $objTransition, $arrData);
                        }
                    }
                }
            }

            $objHandler = $objFlow->getHandler();
            $objHandler->handleStatusTransition($objObject, $objTransition);
        } else {
            throw new \RuntimeException("Invalid transition id");
        }

        // redirect
        if (empty($strRedirect)) {
            $this->adminReload(Link::getLinkAdminHref($objObject->getArrModule("modul"), "list", "&systemid=" . $objObject->getStrPrevId()));
        } else {
            $this->adminReload($strRedirect);
        }

        return "";
    }

    protected function getActionNameForClass($strAction, $objInstance)
    {
        if ($objInstance instanceof FlowActionAbstract) {
            return $strAction . "TransitionAction";
        } elseif ($objInstance instanceof FlowConditionAbstract) {
            return $strAction . "TransitionCondition";
        } else {
            return parent::getActionNameForClass($strAction, $objInstance);
        }
    }

    /**
     * @param $strSystemId
     * @return FlowConfig|null
     */
    private function getFlowFromSystemId($strSystemId)
    {
        if (!validateSystemid($strSystemId)) {
            return null;
        }

        $objFlow = null;
        $objObject = Objectfactory::getInstance()->getObject($strSystemId);
        $arrSystemIds = $objObject->getPathArray();
        foreach ($arrSystemIds as $strSystemId) {
            $objObject = $this->objFactory->getObject($strSystemId);
            if ($objObject instanceof FlowConfig) {
                return $objObject;
            }
        }
        return null;
    }
}
