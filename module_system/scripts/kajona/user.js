/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 *
 * @module user
 */
define("user", ["jquery", "ajax", "statusDisplay"], function($, ajax, statusDisplay){

    /** @exports user */
    var user = {

        /**
         * Adds a user to a single group
         * @param groupid
         * @param userid
         * @returns {boolean}
         */
        addGroupToUser: function(groupid, userid) {
            if ($('tr[data-systemid='+groupid+']').length > 0) {
                return true;
            }

            ajax.genericAjaxCall("user", "apiGroupMemberAdd", "&userid="+userid+"&groupid="+groupid, function(data, status) {
                if (status == 'success') {
                    $('.admintable').append(data.row);
                    $('#group_add_id').val("");
                    $('#group_add').val("");
                    statusDisplay.messageOK(data.message);
                } else {
                    statusDisplay.messageError(data);
                }
            }, null, null, 'post', 'json');
        },

        /**
         * Removes a user from a given group
         * @param groupid
         * @param userid
         */
        removeGroupFromUser: function(groupid, userid) {
            ajax.genericAjaxCall("user", "apiGroupMemberDelete", "&userid="+userid+"&groupid="+groupid, function(data, status) {
                if (status == 'success') {
                    $('tr[data-systemid='+groupid+']').closest('tbody').remove();
                }
            });
        }

    };

    return user;

});