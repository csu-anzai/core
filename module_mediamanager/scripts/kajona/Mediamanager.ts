///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="mediamanager"/>

import * as $ from "jquery";
import Ajax = require("../../../module_system/scripts/kajona/Ajax");
import StatusDisplay = require("../../../module_system/scripts/kajona/StatusDisplay");

class Mediamanager {

    public static createFolder(strInputId: string, strRepoId: string) {
        var strNewFoldername = $("#" + strInputId).val();
        if (strNewFoldername != "") {
            this.createFolderBackend(strRepoId, strNewFoldername+"");
        }
    }

    private static createFolderBackend(strFmRepoId: string, strFolder: string) {
        Ajax.genericAjaxCall("mediamanager", "createFolder", strFmRepoId + "&folder=" + strFolder, function (data: any, status: string, jqXHR: XMLHttpRequest) {
            if (status == 'success') {
                //check if answer contains an error
                if (data.indexOf("<error>") != -1) {
                    StatusDisplay.displayXMLMessage(data);
                }
                else {
                    Ajax.genericAjaxCall("mediamanager", "partialSyncRepo", strFmRepoId, function (data: any, status: string, jqXHR: XMLHttpRequest) {
                        if (status == 'success')
                            location.reload();
                        else
                            StatusDisplay.messageError("<b>Request failed!</b><br />" + data);
                    });
                }
            }
            else {
                StatusDisplay.messageError("<b>Request failed!</b><br />" + data);
            }
        })
    }

    public static editFileMark (systemId: string, newIconNumber: number) {
        Ajax.loadUrlToElement("tbody.template-upload[data-uploadid='"+systemId+"'] .file-details .mark a.navbar-link", "/xml.php?admin=1&module=mediamanager&action=apiFileMarksUpdate&systemid="+systemId+"&iconNumber="+newIconNumber);
    }

}

export = Mediamanager;
