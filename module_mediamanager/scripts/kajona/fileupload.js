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
define(["jquery", "ajax", "forms", "lang", 'blueimp-tmpl', 'jquery-ui/ui/widget', 'jquery.iframe-transport', 'jquery.fileupload', 'jquery.fileupload-process', 'jquery.fileupload-ui'], function($, ajax, forms, lang) {


    var UploadManager = function (options) {

        var settings = $.extend({
            baseElement: null,
            uploadUrl: KAJONA_WEBPATH+'/xml.php?admin=1&module=mediamanager&action=fileUpload',
            autoUpload: false,
            paramName: 'files',
            formData: [],
            readOnly: false,
            multiUpload: true,
            maxFileSize: 0,
            acceptFileTypes: '',
            downloadTemplate: null,
            uploadTemplate: null

        }, options );


        var optionsMerged = {
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
        };

        if (settings.autoUpload && !settings.multiUpload) {
            optionsMerged.add = function(e, data) {

                if (!settings.multiUpload && (settings.baseElement.find('.drop-zone >').length >= 1 || data.originalFiles.length > 1)) {
                    forms.addHint(settings.baseElement.find('.files').attr('id'), "<span data-lang-property='mediamanager:upload_multiple_not_allowed'></span>");
                    lang.initializeProperties(settings.baseElement.find('.files').closest(".form-group"));
                    // alert('single upload only');
                    e.preventDefault();
                    return false;
                }

                if (data.autoUpload || (data.autoUpload !== false && $(this).fileupload('option', 'autoUpload'))) {
                    data.process().done(function () {
                        data.submit();
                    });
                }
            };
        }

        var uploader = settings.baseElement.fileupload(optionsMerged);

        if (settings.readOnly) {
            settings.baseElement.fileupload('disable');
        }

        return {
            /**
             * Get the upload instance
             */
            getUploader : function() {
                return uploader;
            },

            /**
             * Query the backend to version all files
             */
            fileVersioning : function() {
                var me = this;
                ajax.genericAjaxCall("mediamanager", "documentVersioning", "&systemid="+settings.formData[0].value+"&folder="+settings.formData[2].value, function(e) {
                    if (e.status && e.status === "ok") {
                        //in case of success, flush the list
                        settings.baseElement.find('.files').empty();
                        me.renderArchiveList();
                    }
                }, null, null, "post", "json");
            },

            renderArchiveList : function() {
                ajax.loadUrlToElement(settings.baseElement.find(".archive-list"), "/xml.php?admin=1&module=mediamanager&action=getArchiveList&systemid="+settings.formData[0].value+"&folder="+settings.formData[2].value);
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
            var dropZone = $('.fileupload-wrapper:not(.blueimp-fileupload-disabled) .drop-zone'),
                timeout = window.dropZoneTimeout;
            if (!timeout) {
                dropZone.addClass('active-dropzone');
            } else {
                clearTimeout(timeout);
            }

            var curDropZone = e.target.closest('.fileupload-wrapper:not(.blueimp-fileupload-disabled) .drop-zone');
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

