<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Flow\System;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryHeadline;
use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\Alert\MessagingAlertActionRedirect;
use Kajona\System\System\Alert\MessagingAlertActionVoid;
use Kajona\System\System\Carrier;
use Kajona\System\System\Link;
use Kajona\System\System\MessagingAlert;
use Kajona\System\System\MessagingMessagehandler;
use Kajona\System\System\Model;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\RedirectException;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\Session;
use Kajona\System\Xml;

/**
 * @author christoph.kappestein@artemeon.de
 * @module flow
 */
trait FlowControllerTrait
{
    /**
     * @inject flow_manager
     * @var FlowManager
     */
    protected $objFlowManager;

    /**
     * @inject system_message_handler
     * @var MessagingMessagehandler
     */
    protected $objMessageHandler;

    /**
     * @inject system_session
     * @var Session
     */
    protected $objSession;

    protected function renderStatusAction(Model $objListEntry, $strAltActive = "", $strAltInactive = "")
    {
        if ($objListEntry->getIntRecordDeleted() == 1) {
            return "";
        }

        $objCurrentStatus = $this->objFlowManager->getCurrentStepForModel($objListEntry);
        if ($objCurrentStatus === null) {
            return parent::renderStatusAction($objListEntry, $strAltActive, $strAltInactive);
        }

        $strIcon = AdminskinHelper::getAdminImage($objCurrentStatus->getStrIcon(), $objCurrentStatus->getStrDisplayName());

        if (!$objListEntry->rightView()) {
            return "";
        }

        $strMenuId = "status-menu-" . generateSystemid();
        $strDropdownId = "status-dropdown-" . generateSystemid();
        $strReturn = $this->objToolkit->listButton(
            "<span class='dropdown status-dropdown' id='" . $strDropdownId . "'><a href='#' data-toggle='dropdown' role='button'>" . $strIcon . "</a><div class='dropdown-menu generalContextMenu' role='menu' id='" . $strMenuId . "'></div></span>"
        );

        $strParams = http_build_query(["admin" => 1, "module" => $objListEntry->getArrModule('module'), "action" => "showStatusMenu", "systemid" => $objListEntry->getSystemid()], null, "&");
        $strReturn .= '<script type="text/javascript">
require(["jquery", "ajax"], function($, ajax){
    $("#' . $strDropdownId . '").on("show.bs.dropdown", function () {
        ajax.loadUrlToElement("#' . $strMenuId . '", "/xml.php?' . $strParams . '");
    });
});
</script>';

        return $strReturn;
    }

    /**
     * Action to set the next status
     *
     * @return string
     * @permissions view
     */
    protected function actionSetStatus()
    {
        $objObject = $this->objFactory->getObject($this->getSystemid());
        if ($objObject instanceof Model) {
            // check right
            if ($objObject instanceof FlowModelRightInterface) {
                $bitHasRight = $objObject->rightStatus();
            } else {
                $bitHasRight = $objObject->rightEdit();
            }

            if (!$bitHasRight) {
                throw new \RuntimeException("No right to change the status of the object");
            }

            $strTransitionId = $this->getParam("transition_id");
            $objFlow = $this->objFlowManager->getFlowForModel($objObject);
            $objTransition = Objectfactory::getInstance()->getObject($strTransitionId);

            if ($objTransition instanceof FlowTransition) {
                $arrActions = $objTransition->getArrActions();
                $objForm = new AdminFormgenerator("", $objObject);
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
                        $strForm = $objForm->renderForm(Link::getLinkAdminHref($this->getArrModule("modul"), "setStatus", "&systemid=" . $objObject->getStrSystemid() . "&transition_id=" . $strTransitionId));
                        return $strForm;
                    } else {
                        foreach ($arrActions as $objAction) {
                            if ($objAction instanceof FlowActionUserInputInterface) {
                                $objActionForm = new AdminFormgenerator("", $objObject);
                                $objAction->configureUserInputForm($objActionForm);
                                $objAction->handleUserInput($objObject, $objTransition, $objActionForm);

                                // in case the handleUserInput added a validation error
                                if (!$objActionForm->validateForm()) {
                                    $strForm = $objActionForm->renderForm(Link::getLinkAdminHref($this->getArrModule("modul"), "setStatus", "&systemid=" . $objObject->getStrSystemid() . "&transition_id=" . $strTransitionId));
                                    return $strForm;
                                }
                            }
                        }
                    }
                }

                $objHandler = $objFlow->getHandler();

                try {
                    $objHandler->handleStatusTransition($objObject, $objTransition);
                    $this->adminReload(Link::getLinkAdminHref($this->getArrModule("modul"), "list", "&systemid=" . $objObject->getStrPrevId()));
                } catch (RedirectException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    $objToolkit = Carrier::getInstance()->getObjToolkit("admin");
                    return $objToolkit->warningBox($e->getMessage());
                }
            }
        }

        return "";
    }

    /**
     * Renders the status menu
     *
     * @return string
     * @permissions view
     * @responseType html
     */
    protected function actionShowStatusMenu()
    {
        Xml::setBitSuppressXmlHeader(true);

        $objObject = Objectfactory::getInstance()->getObject($this->getSystemid());

        if ($objObject->getIntRecordDeleted() == 1) {
            return "";
        }

        if (!$objObject->rightView()) {
            return "<ul><li class='dropdown-header'>" . $this->getLang("list_flow_no_right", "flow") . "</li></ul>";
        }

        $arrMenu = array();

        $strLink = htmlspecialchars(Link::getLinkAdminHref("flow", "showFlow", ["systemid" => $this->getSystemid(), "folderview" => "1"]));
        $strTitle = $this->getLang("flow_current_status", "flow");
        $arrMenu[] = array(
            "fullentry" => '<li><a href="#" onclick="require(\'dialogHelper\').showIframeDialog(\''.$strLink.'\', \''.$strTitle.'\'); return false;">'.$strTitle.'</a></li><li role="separator" class="divider"></li>',
        );

        $strClass = $objObject->getSystemid() . "-errors";
        $arrTransitions = $this->objFlowManager->getPossibleTransitionsForModel($objObject, false);
        $objFlow = $this->objFlowManager->getFlowForModel($objObject);

        // check right
        if ($objObject instanceof FlowModelRightInterface) {
            $bitHasRight = $objObject->rightStatus();
        } else {
            $bitHasRight = $objObject->rightEdit();
        }

        if (!empty($arrTransitions) && $bitHasRight) {
            foreach ($arrTransitions as $objTransition) {
                // skip if not visible
                if (!$objTransition->isVisible()) {
                    continue;
                }

                /** @var FlowTransition $objTransition */
                $objTargetStatus = $objTransition->getTargetStatus();

                // validation
                $objResult = $objFlow->getHandler()->validateStatusTransition($objObject, $objTransition);

                $strValidation = "";
                if (!$objResult->isValid()) {
                    $arrErrors = $objResult->getErrors();
                    if (!empty($arrErrors)) {
                        $strTooltip = "<div class='alert alert-danger'>";
                        $strTooltip.= "<ul>";
                        foreach ($arrErrors as $strError) {
                            if (!empty($strError)) {
                                $strError = htmlspecialchars($strError);
                                $strTooltip.= "<li>{$strError}</li>";
                            }
                        }
                        $strTooltip.= "</ul>";
                        $strTooltip.= "</div>";
                        $strValidation.= '<span class="' . $strClass . '" data-validation-errors="' . $strTooltip . '"></span>';
                    } else {
                        // in case the result is not valid and we have no error message we skip the menu entry
                        continue;
                    }
                }

                if (!empty($strValidation)) {
                    $arrMenu[] = array(
                        "name" => AdminskinHelper::getAdminImage("icon_flag_hex_disabled_" . $objTargetStatus->getStrIconColor()) . " " . $objTargetStatus->getStrDisplayName() . $strValidation,
                        "link" => "#",
                    );
                } else {
                    $arrMenu[] = array(
                        "name" => AdminskinHelper::getAdminImage($objTargetStatus->getStrIcon()) . " " . $objTargetStatus->getStrDisplayName(),
                        "link" => Link::getLinkAdminHref($this->getArrModule("modul"), "setStatus", "&systemid=" . $objObject->getStrSystemid() . "&transition_id=" . $objTransition->getSystemid()),
                    );
                }
            }
        }

        if (!empty($arrMenu)) {
            $strHtml = $this->objToolkit->registerMenu(generateSystemid(), $arrMenu);

            // hack to remove the div around the ul since the div is already in the html
            preg_match("#<ul>(.*)</ul>#ims", $strHtml, $arrMatches);

            // js to init the tooltip for validation errors
            $strTitle = json_encode($objObject->getStrDisplayName());
            $strJs = <<<HTML
<script type='text/javascript'>
    require(['jquery', 'dialogHelper'], function($, dialogHelper){
        $('.{$strClass}').parent().on('click', function(){
            var errors = $(this).find('.{$strClass}').data('validation-errors');
            dialogHelper.showConfirmationDialog({$strTitle}, errors, "OK", function(){
                $('#jsDialog_1').modal('hide');
            });
        });
    });
</script>
HTML;

            return $arrMatches[0] . $strJs;
        } else {
            return "<ul><li class='dropdown-header'>" . $this->getLang("list_flow_no_status", "flow") . "</li></ul>";
        }
    }

    /**
     * Ajax endpoint to trigger a status transition
     *
     * @return string
     * @permissions view
     * @responseType json
     * @xml
     */
    protected function actionApiSetFlowStatus()
    {
        $objObject = $this->objFactory->getObject($this->getSystemid());
        $objAlert = null;

        if ($objObject instanceof Model) {
            // check right
            if ($objObject instanceof FlowModelRightInterface) {
                $bitHasRight = $objObject->rightStatus();
            } else {
                $bitHasRight = $objObject->rightEdit();
            }

            if (!$bitHasRight) {
                return json_encode(["success" => false, "message" => $this->getLang("commons_error_permissions", "commons")]);
            }

            $strTransitionId = $this->getParam("transition_id");
            $objFlow = $this->objFlowManager->getFlowForModel($objObject);
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
                    // in this case an action needs additional user input so we redirect the user to the form
                    $strRedirect = Link::getLinkAdminHref($this->getArrModule("modul"), "setStatus", "&systemid=" . $objObject->getStrSystemid() . "&transition_id=" . $strTransitionId);

                    $objAlert = new MessagingAlert();
                    $objAlert->setStrTitle($this->getLang("action_status_change_title", "flow"));
                    $objAlert->setStrBody($this->getLang("action_status_change_redirect", "flow"));
                    $objAlert->setObjAlertAction(new MessagingAlertActionRedirect($strRedirect));
                } else {
                    try {
                        // validation
                        $objResult = $objFlow->getHandler()->validateStatusTransition($objObject, $objTransition);

                        if (!$objResult->isValid()) {
                            $arrErrors = $objResult->getErrors();
                            if (!empty($arrErrors)) {
                                $strTooltip = "<ul>";
                                foreach ($arrErrors as $strError) {
                                    if (!empty($strError)) {
                                        $strError = htmlspecialchars($strError);
                                        $strTooltip.= "<li>{$strError}</li>";
                                    }
                                }
                                $strTooltip.= "</ul>";

                                $objAlert = new MessagingAlert();
                                $objAlert->setStrTitle($this->getLang("action_status_change_title", "flow"));
                                $objAlert->setStrBody($this->objToolkit->warningBox($strTooltip, "alert-danger"));
                                $objAlert->setObjAlertAction(new MessagingAlertActionVoid());
                            }
                        } else {
                            // execute status transition
                            $objFlow->getHandler()->handleStatusTransition($objObject, $objTransition);

                            $objAlert = new MessagingAlert();
                            $objAlert->setStrTitle($this->getLang("action_status_change_title", "flow"));
                            $objAlert->setStrBody($this->getLang("action_status_change_success", "flow"));
                            $objAlert->setObjAlertAction(new MessagingAlertActionVoid());

                            $strRedirectUrl = ResponseObject::getInstance()->getStrRedirectUrl();
                            if (!empty($strRedirectUrl)) {
                                $objAlert->setObjAlertAction(new MessagingAlertActionRedirect($strRedirectUrl));
                                ResponseObject::getInstance()->setStrRedirectUrl("");
                            }
                        }

                    } catch (RedirectException $e) {
                        $objAlert = new MessagingAlert();
                        $objAlert->setStrTitle($this->getLang("action_status_change_title", "flow"));
                        $objAlert->setStrBody($this->getLang("action_status_change_success", "flow"));
                        $objAlert->setObjAlertAction(new MessagingAlertActionRedirect($e->getHref()));
                    } catch (\Exception $e) {
                        $objAlert = new MessagingAlert();
                        $objAlert->setStrTitle($this->getLang("action_status_change_title", "flow"));
                        $objAlert->setStrBody($this->objToolkit->warningBox($e->getMessage(), "alert-danger"));
                        $objAlert->setObjAlertAction(new MessagingAlertActionVoid());
                    }
                }
            }
        }

        if ($objAlert instanceof MessagingAlert) {
            $objAlert->setIntPriority(9);
            $this->objMessageHandler->sendAlertToUser($objAlert, $this->objSession->getUser());
        }

        return json_encode(["success" => true]);
    }
}
