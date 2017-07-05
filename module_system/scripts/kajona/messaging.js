/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 * Subsystem for all messaging related tasks. Queries the backend for the number of unread messages, ...
 *
 * @module messaging
 */
define('messaging', ['jquery', 'ajax'], function ($, ajax) {

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
                    me.intCount = $.parseJSON(data);
                    objCallback(me.intCount);
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
