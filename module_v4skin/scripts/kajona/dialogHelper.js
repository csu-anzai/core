

define('dialogHelper', ['jquery', 'dialog'], function ($, Dialog) {

    var loadingModal = null;


    return /** @alias module:dialogHelper */ {
        /**
         * Creates a new confirmation dialog
         *
         * @param strTitle
         * @param strContent
         * @param strConfirmationLabel
         * @param strConfirmationHref
         * @returns {module:dialog}
         */
        showConfirmationDialog : function(strTitle, strContent, strConfirmationLabel, strConfirmationHref) {
            var dialogInstance = new Dialog('jsDialog_1', 1);
            dialogInstance.setTitle(strTitle);
            dialogInstance.setContent(strContent, strConfirmationLabel, strConfirmationHref);
            dialogInstance.init();
            return dialogInstance;
        },

        /**
         * Opens an iframe based dialog to load other pages within a dialog. saves the dialog reference to folderview.dialog
         * in order to modify / access it later
         *
         * @param strUrl
         * @param strTitle
         * @returns {module:dialog}
         */
        showIframeDialog : function(strUrl, strTitle) {
            var dialogInstance = new Dialog('folderviewDialog', 0);
            dialogInstance.setContentIFrame(strUrl);
            dialogInstance.setTitle(strTitle);
            dialogInstance.init();

            //register the dialog
            require(['folderview'], function(folderview) {
                folderview.dialog = dialogInstance;
            });

            return dialogInstance;

        },

        /**
         * Registers and shows a loading modal
         * @returns {module:dialog}
         */
        showLoadingModal : function() {

            if (loadingModal === null) {
                loadingModal = new Dialog('jsDialog_3', 3);
            }

            loadingModal.init();
            return loadingModal;
        },

        /**
         * Hides the currently open loading modal
         */
        hideLoadingModal : function() {
            if (loadingModal instanceof Dialog) {
                loadingModal.hide();
            }
        }
    };

});
