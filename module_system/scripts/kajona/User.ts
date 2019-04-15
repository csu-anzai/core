///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="user"/>

import * as $ from "jquery";
import Ajax = require("./Ajax");
import StatusDisplay = require("./StatusDisplay");

class User {

    /**
     * Adds a user to a single group
     * @param groupid
     * @param userid
     * @returns {boolean}
     */
    public static addGroupToUser(groupid: string, userid: string) {
        if ($('tr[data-systemid='+groupid+']').length > 0) {
            return true;
        }

        Ajax.genericAjaxCall("user", "apiGroupMemberAdd", "&userid="+userid+"&groupid="+groupid, function(data: any, status: string) {
            if (status == 'success') {
                // $('.admintable').append(data.row);
                $('.admintable tbody').last().before(data.row);
                $('#group_add_id').val("");
                $('#group_add').val("");
                StatusDisplay.messageOK(data.message);
            } else {
                StatusDisplay.messageError(data);
            }
        }, null, null, 'post', 'json');
    };

    /**
     * Removes a user from a given group
     * @param groupid
     * @param userid
     */
    public static removeGroupFromUser(groupid: string, userid: string) {

        Ajax.genericAjaxCall("user", "apiGroupMemberDelete", "&userid="+userid+"&groupid="+groupid, function(data: any, status: string) {
            if (status == 'success') {
                $('tr[data-systemid='+groupid+']').closest('tbody').remove();
            }
        });
    };

}

export = User;
