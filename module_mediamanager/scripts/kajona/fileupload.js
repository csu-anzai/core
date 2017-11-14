//   (c) 2004-2006 by MulchProductions, www.mulchprod.de
//   (c) 2007-2016 by Kajona, www.kajona.de
//       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt

/**
 * Helper for fileupload management.
 * Wraps the jquery Fileupload plugin
 *
 * the options object expects:
 *
 * @module fileupload
 */
define(["jquery", "ajax", 'blueimp-tmpl', 'jquery-ui/ui/widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-ui'], function($, ajax) {


    UploadManager = function (options) {

        var settings = $.extend({
            baseElement: null,
            uploadUrl: KAJONA_WEBPATH+'/xml.php?admin=1&module=mediamanager&action=fileUpload',
            autoUpload: false,
            paramName: 'files',
            formData: [],
            maxFileSize: 0,
            acceptFileTypes: '',
            downloadTemplate: null,
            uploadTemplate: null

        }, options );


        var uploader = settings.baseElement.fileupload({
            url: settings.uploadUrl,
            dataType: 'json',
            dropZone: settings.baseElement.find('.drop-zone'),
            pasteZone: $(document),
            autoUpload: settings.autoUpload,
            paramName : settings.paramName,
            filesContainer: settings.baseElement.find('.files'),
            formData: settings.formData,
            // messages: {
            //     maxNumberOfFiles: 'Maximum number of files exceeded',
            //     acceptFileTypes: "[lang,upload_fehler_filter,mediamanager]",
            //     maxFileSize: "[lang,upload_multiple_errorFilesize,mediamanager]",
            //     minFileSize: 'File is too small'
            // },
            maxFileSize: settings.maxFileSize,
            acceptFileTypes: settings.acceptFileTypes,
            uploadTemplateId: null,
            downloadTemplateId: null,
            downloadTemplate: settings.downloadTemplate,
            uploadTemplate: settings.uploadTemplate
        });

        return {
            getUploader : function() {
                return uploader;
            }
        }

    };

    var dropoverInitialized = false;
    var initDragover = function() {

        //only once, plz
        if (dropoverInitialized) {
            return;
        }

        $(document).bind('dragover', function (e) {
            var dropZone = $('.drop-zone'),
                timeout = window.dropZoneTimeout;
            if (!timeout) {
                dropZone.addClass('active-dropzone');

            } else {
                clearTimeout(timeout);
            }
            var found = false,
                node = e.target;
            do {
                if (node === dropZone[0]) {
                    found = true;
                    break;
                }
                node = node.parentNode;
            } while (node != null);
            if (found) {
                dropZone.addClass('hover');
            } else {
                dropZone.removeClass('hover');
            }
            window.dropZoneTimeout = setTimeout(function () {
                window.dropZoneTimeout = null;
                dropZone.removeClass('active-dropzone hover');
            }, 100);
        });

        dropoverInitialized = true;
    };



    return {
        /**
         * Callback used when deleting a file
         * @param strFileId
         */
        deleteFile : function (strFileId) {
            ajax.genericAjaxCall("system", "delete", strFileId, null, function() {
                $('tbody.template-upload[data-uploadid="'+strFileId+'"]').remove();
            });
        },

        /**
         * Inits the uploader and returns an instance.
         * Makes sure to init the dragover functions, too
         * @param options
         * @returns {UploadManager}
         */
        initUploader : function (options) {
            var uploader = new UploadManager(options);
            initDragover();
            return uploader;
        }
    };
});

