/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 * Subsystem for all messaging related tasks. Queries the backend for the number of unread messages, ...
 *
 * @module messaging
 */
define('messaging', ['jquery', 'ajax', 'dialogHelper'], function ($, ajax, dialogHelper) {

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
        }

        if ($onAccept.type === 'ajax') {
            return function() {
                ajax.genericAjaxCall($onAccept.module, $onAccept.action, $onAccept.systemid);
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

    return /** @alias module:messaging */ {
        properties: null,
        bitFirstLoad : true,
        intCount : 0,

        /**
         * Gets the number of unread messages for the current user.
         * Expects a callback-function whereas the number is passed as a param.
         *
         * @param objCallback
         */
        getUnreadCount : function(objCallback) {
            var me = this;
            ajax.genericAjaxCall("messaging", "getUnreadMessagesCount", "", function(data, status, jqXHR) {
                if(status == 'success') {
                    var $objResult = $.parseJSON(data);
                    me.intCount = $objResult.count;
                    objCallback(me.intCount);

                    if($objResult.alert) {
                        renderAlert($objResult.alert);
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
        }
    };

});
