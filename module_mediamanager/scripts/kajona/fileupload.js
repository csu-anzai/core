//   (c) 2004-2006 by MulchProductions, www.mulchprod.de
//   (c) 2007-2016 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt


define(["jquery", "ajax", "statusDisplay"], function($, ajax, statusDisplay) {



    var fileupload = {};

    /**
     * deletes a file form the list of uploaded files
     * @param strFileId
     */
    fileupload.deleteFile = function (strFileId) {
        ajax.genericAjaxCall("system", "delete", strFileId, null, function() {
            $('tbody.template-upload[data-uploadid="'+strFileId+'"]').remove();
        });
    };


    return fileupload;
});

