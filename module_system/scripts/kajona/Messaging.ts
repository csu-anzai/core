///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="messaging"/>

import * as $ from "jquery";
import * as toastr from "toastr";
import Ajax = require("./Ajax");
import Util = require("./Util");
import DialogHelper = require("../../../module_v4skin/scripts/kajona/DialogHelper");
import Router = require("./Router");
import Dialog = require("../../../module_v4skin/scripts/kajona/Dialog");

interface Accept {
    type: string
    module: string
    action: string
    systemid: string
    target: string
}

interface Alert {
    type: string
    systemid: string
    title: string
    body: string
    onAccept: Accept
    confirmLabel?: string
}

/**
 * Subsystem for all messaging related tasks. Queries the backend for the number of unread messages, ...
 */
class Messaging {

    private static pollInterval : number = 30000;
    private static timeout : number = null;

    private static intCount : number = 0;
    private static dialog : Dialog;

    private static properties : boolean = null;
    private static bitFirstLoad : boolean = true;

    /**
     * Forces a polling of unread messages and alert, but only once
     */
    public static pollMessages() {
        Messaging.getUnreadCount(function (intCount : number) {
            Messaging.updateCountInfo(intCount);
        });
    }


    /**
     * Gets the number of unread messages for the current user.
     * Expects a callback-function whereas the number is passed as a param.
     *
     * @param objCallback
     */
    public static getUnreadCount(objCallback : Function) {
        // in case we are on the login page dont poll
        if ($('#loginContainer').length > 0) {
            return;
        }

        Ajax.genericAjaxCall("messaging", "getUnreadMessagesCount", "", function(data : any, status : string, jqXHR : any) {
            if(status == 'success') {
                var $objResult = $.parseJSON(data);
                objCallback($objResult.count);

                if($objResult.alert) {
                    Messaging.renderAlert($objResult.alert);
                }
            } else {
                // in case the API returns a 401 the user has logged out so reload the page to show the login page
                if (data.status == 401 && $('#loginContainer').length == 0 && !$('body').hasClass('anonymous')) {
                    location.reload();
                }
            }
        });
    }

    /**
     * Loads the list of recent messages for the current user.
     * The callback is passed the json-object as a param.
     * @param objCallback
     */
    public static getRecentMessages(objCallback : Function) {
        Ajax.genericAjaxCall("messaging", "getRecentMessages", "", function(data : any, status : string, jqXHR : any) {
            if(status == 'success') {
                var objResponse = $.parseJSON(data);
                objCallback(objResponse);
            }
        });
    }

    /**
     * Enables or disables the polling of message counts / alerts
     * @param bitEnabled
     */
    public static setPollingEnabled(bitEnabled : boolean) {
        if(Util.isStackedDialog() || $('body').hasClass('anonymous')) {
            bitEnabled = false;
        }

        if (bitEnabled) {
            // start timeout only if we have not already a timeout
            if (!Messaging.timeout) {
                Messaging.pollMessageCount();
            }
        } else {
            // if we have a timeout clear
            if (Messaging.timeout) {
                window.clearTimeout(Messaging.timeout);
            }
            Messaging.timeout = null;
        }

    }


    /**
     * Updates the count info of the current unread messages
     * @param intCount
     */
    public static updateCountInfo(intCount : number) {
        var $userNotificationsCount = $('#userNotificationsCount');
        var oldCount = parseInt($userNotificationsCount.text());
        $userNotificationsCount.text(intCount);
        if (intCount > 0) {
            $userNotificationsCount.show();
            if (oldCount != intCount) {
                if (document.title.match(/\(\d+\)/)) {
                    document.title = document.title.replace(/\(\d+\)/, "(" + intCount + ")");
                } else {
                    document.title = "(" + intCount + ") " + document.title;
                }
            }
        } else {
            $userNotificationsCount.hide();
        }

    }

    private static registerListener() {

        // listen to browser events to enable/disable notification polling if window is not active
        $(window).focus(function(){
            Messaging.setPollingEnabled(true);
        });

        $(window).blur(function(){
            Messaging.setPollingEnabled(false);
        });


    }

    /**
     * Triggers the polling of unread messages from the backend
     */
    private static pollMessageCount() {
        Messaging.getUnreadCount(function (intCount : number) {
            Messaging.updateCountInfo(intCount);
        });

        Messaging.timeout = window.setTimeout(Messaging.pollMessageCount, Messaging.pollInterval);
    }

    /**
     * Renders an alert generated on the backend
     * @param $objAlert
     */
    private static renderAlert($objAlert : Alert) {
        if ($objAlert.type === "Kajona\\System\\System\\MessagingNotification") {
            var options = {
                onclick: function(){
                    let callback = Messaging.getActionCallback($objAlert.onAccept);
                    callback();
                }
            };

            toastr.info($objAlert.title, $objAlert.body, options);

            Messaging.updateExistingStatusIcons();
        } else {
            if (!Messaging.dialog || (Messaging.dialog && !Messaging.dialog.isVisible())) {
                Messaging.dialog = DialogHelper.showConfirmationDialog($objAlert.title, $objAlert.body, $objAlert.confirmLabel, Messaging.getActionCallback($objAlert.onAccept));
            }
        }
        Ajax.genericAjaxCall("messaging", "deleteAlert", $objAlert.systemid);
    }

    /**
     * Internal helper to built a real callback based on the action provided by the backend
     * @param $onAccept
     * @returns {Function}
     */
    private static getActionCallback($onAccept : Accept) : Function {

        if ($onAccept && $onAccept.type === 'redirect') {
            return function() {
                Router.registerLoadCallback("alert_redirect", function() {
                    $('.modal-backdrop.fade.in').remove();
                    Messaging.pollMessages();
                });

                if (Messaging.dialog) {
                    Messaging.dialog.hide();
                }
                Router.loadUrl($onAccept.target);

            };
        } else if ($onAccept && $onAccept.type === 'ajax') {
            return function() {
                Ajax.genericAjaxCall($onAccept.module, $onAccept.action, $onAccept.systemid, function(){
                    // on ok we trigger the getUnreadCount again since the ajax call could have created
                    // other alert messages
                    Messaging.pollMessages();
                });
            };
        }

        return function() { };
    };

    /**
     * Tries to find and update all existing status icons on a page
     */
    private static updateExistingStatusIcons() {
        $(".flow-status-icon").each(function(){
            let el = $(this).find(".navbar-link");
            Ajax.genericAjaxCall("flow", "apiGetStatusIcon", $(this).data("systemid"), function(resp : any){
                let data = JSON.parse(resp);
                el.html(data.html);
            });
        });
    }
}

export = Messaging;
