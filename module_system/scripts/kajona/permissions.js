/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 * Little helper function for the system permissions matrix
 *
 * @module permissions
 */
define("permissions", ["jquery", "ajax"], function($, ajax){

    /** @exports permissions */
    var perms = {

        submitForm: function() {
            var objResponse = {
                bitInherited : $("#inherit").is(":checked"),
                arrConfigs : []
            };

            $(".core-component-rights tbody > tr").find('input:checked').each(function(){
                objResponse.arrConfigs.push($(this).attr('id'));
            });

            // disable submit button
            $(".savechanges").addClass("processing");
            $(".savechanges").prop("disabled", true);

            $.ajax({
                url: KAJONA_WEBPATH + '/xml.php?admin=1&module=right&action=saveRights&systemid='+ $('#systemid').val(),
                type: 'POST',
                data: JSON.stringify(objResponse),
                contentType: 'application/json',
                dataType: 'json'
            }).done(function(data) {
                // enable submit button
                $(".savechanges").removeClass("processing");
                $(".savechanges").prop("disabled", false);

                // load rights
                perms.loadRights();
            });

            return false;
        },

        toggleInherit: function(){
            $(".core-component-rights").find("input[type='checkbox']").each(function(){
                $(this).prop("disabled", perms.isInherited());
                $("#group_add").prop("disabled", perms.isInherited());

                $(".core-component-rights").find(".fa-trash-o").each(function(){
                    if (perms.isInherited()) {
                        $(this).addClass("text-muted");
                    } else {
                        $(this).removeClass("text-muted");
                    }
                });
            });
        },

        isInherited: function(){
            return $("#inherit").prop("checked");
        },

        addGroup: function(groupId){
            if (!groupId) {
                return;
            }

            if (perms.isInherited()) {
                return;
            }

            if (perms.hasGroup(groupId)) {
                return;
            }

            var groupName = $('#group_add').val();

            var row = "";
            row+= "<tr data-groupid='" + groupId + "'>";
            row+= "<td><a href=\"#\" onclick=\"require('permissions').removeGroup('" + groupId + "',this);return false;\"><i class=\"kj-icon fa fa-trash-o\" ></i></a></td>";
            row+= "<td>" + groupName + "</td>";

            $(".core-component-rights thead > tr > th").each(function(){
                var right = $(this).data("right");
                if (right) {
                    row+= "<td><input rel=\"tooltip\" type=\"checkbox\" name=\"" + right + "," + groupId + "\" id=\"" + right + "," + groupId + "\" value=\"1\"></td>";
                }
            });

            row+= "</tr>";
            $(".core-component-rights").find("tbody").append(row);

            setTimeout(function(){
                $('#group_add').val('');
                $('#group_add_id').val('');
            }, 100);
        },

        removeGroup: function(groupId, el){
            if (perms.isInherited()) {
                return;
            }

            $(el).closest("tr").remove();
        },

        hasGroup: function(groupId){
            return $(".core-component-rights tbody > tr[data-groupid='" + groupId + "']").length > 0;
        },

        loadRights: function(){
            ajax.loadUrlToElement("#rightsContainer", KAJONA_WEBPATH + '/xml.php?module=right&action=loadRights&systemid=' + $('#systemid').val() + '&folderview=1', "", true, "GET", function(){
                perms.toggleInherit();
            });
        }

    };

    return perms;

});