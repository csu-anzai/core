/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 * Subsystem for all messaging related tasks. Queries the backend for the number of unread messages, ...
 *
 * @module messaging
 */
define('messaging', ['jquery', 'ajax', 'dialogHelper', 'util'], function ($, ajax, dialogHelper, util) {


    var pollInterval = 30000;
    var timeout = null;

    var intCount = 0;

    /**
     * Internal helper to built a real callback based on the action provided by the backend
     * @param $onAccept
     * @returns {Function}
     */
    var getActionCallback = function($onAccept) {

        if ($onAccept.type === 'redirect') {
            return function() {
                document.location.href = $onAccept.target;
            };
        } else if ($onAccept.type === 'ajax') {
            return function() {
                ajax.genericAjaxCall($onAccept.module, $onAccept.action, $onAccept.systemid, function(){
                    // on ok we trigger the getUnreadCount again since the ajax call could have created
                    // other alert messages
                    me.getUnreadCount(function(){
                    });
                });
            };
        }

        return function() {};
    };

    /**
     * Renders an alert generated on the backend
     * @param $objAlert
     */
    var renderAlert = function($objAlert) {
        dialogHelper.showConfirmationDialog($objAlert.title, $objAlert.body, $objAlert.confirmLabel, getActionCallback($objAlert.onAccept));
        ajax.genericAjaxCall("messaging", "deleteAlert", $objAlert.systemid);
    };

    /**
     * Triggers the polling of unread messages from the backend
     */
    var pollMessageCount = function() {
        me.getUnreadCount(function (intCount) {
            me.updateCountInfo(intCount);
        });

        timeout = window.setTimeout(pollMessageCount, pollInterval);
    };

    // listen to browser events to enable/disable notification polling if window is not active
    $(window).focus(function(){
        me.setPollingEnabled(true);
    });

    $(window).blur(function(){
        me.setPollingEnabled(false);
    });

    /** @alias module:messaging */
    var me = {
        properties: null,
        bitFirstLoad : true,

        /**
         * Gets the number of unread messages for the current user.
         * Expects a callback-function whereas the number is passed as a param.
         *
         * @param objCallback
         */
        getUnreadCount : function(objCallback) {
            // in case we are on the login page dont poll
            if ($('#loginContainer').length > 0) {
                return;
            }

            ajax.genericAjaxCall("messaging", "getUnreadMessagesCount", "", function(data, status, jqXHR) {
                if(status == 'success') {
                    var $objResult = $.parseJSON(data);
                    objCallback($objResult.count);

                    if($objResult.alert) {
                        renderAlert($objResult.alert);
                    }
                } else {
                    // in case the API returns a 401 the user has logged out so reload the page to show the login page
                    if (data.status == 401 && $('#loginContainer').length == 0) {
                        location.reload();
                    }
                }
            });
        },

        /**
         * Loads the list of recent messages for the current user.
         * The callback is passed the json-object as a param.
         * @param objCallback
         */
        getRecentMessages : function(objCallback) {
            ajax.genericAjaxCall("messaging", "getRecentMessages", "", function(data, status, jqXHR) {
                if(status == 'success') {
                    var objResponse = $.parseJSON(data);
                    objCallback(objResponse);
                }
            });
        },

        /**
         * Enables or disables the polling of message counts / alerts
         * @param bitEnabled
         */
        setPollingEnabled : function(bitEnabled) {
            if(util.isStackedDialog()) {
                bitEnabled = false;
            }

            if (bitEnabled) {
                // start timeout only if we have not already a timeout
                if (!timeout) {
                    pollMessageCount();
                }
            } else {
                // if we have a timeout clear
                if (timeout) {
                    window.clearTimeout(timeout);
                }
                timeout = null;
            }
        },

        /**
         * Updates the count info of the current unread messages
         * @param intCount
         */
        updateCountInfo: function(intCount) {
            var $userNotificationsCount = $('#userNotificationsCount');
            var oldCount = $userNotificationsCount.text();
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
    };

    return me;
});
