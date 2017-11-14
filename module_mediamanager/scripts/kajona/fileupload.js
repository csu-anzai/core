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
            readOnly: false,
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
            maxFileSize: settings.maxFileSize,
            acceptFileTypes: settings.acceptFileTypes,
            uploadTemplateId: null,
            downloadTemplateId: null,
            downloadTemplate: settings.downloadTemplate,
            uploadTemplate: settings.uploadTemplate
        });

        if (settings.readOnly) {
            settings.baseElement.fileupload('disable');
        }

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
            var dropZone = $('div.fileupload-wrapper:not(.blueimp-fileupload-disabled) table.drop-zone'),
                timeout = window.dropZoneTimeout;
            if (!timeout) {
                dropZone.addClass('active-dropzone');

            } else {
                clearTimeout(timeout);
            }

            var curDropZone = e.target.closest('div.fileupload-wrapper:not(.blueimp-fileupload-disabled) table.drop-zone');
            if (curDropZone) {
                $(curDropZone).addClass('hover');
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

