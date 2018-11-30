/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 * General way to display a status message.
 * Therefore, the html-page should provide the following elements as noted as instance-vars:
 * - div,   id: jsStatusBox    				the box to be animated
 * 		 class: jsStatusBoxMessage			class in case of an informal message
 * 		 class: jsStatusBoxError		    class in case of an error message
 * - div,   id: jsStatusBoxContent			the box to place the message-content into
 *
 * Pass a xml-response from a Kajona server to displayXMLMessage() to start the logic
 * or use messageOK() / messageError() passing a regular string
 *
 * @module statusDisplay
 */
define('statusDisplay', ['jquery', 'toastr'], function ($, toastr) {

    return /** @alias module:statusDisplay */ {
        idOfMessageBox : "jsStatusBox",
        idOfContentBox : "jsStatusBoxContent",
        timeToFadeOutMessage : 3000,
        timeToFadeOutError   : 5000,
        timeToFadeOut : null,

        /**
         * General entrance point. Use this method to pass an xml-response from the kajona server.
         * Tries to find a message- or an error-tag an invokes the corresponding methods
         *
         * @param {String} message
         */
        displayXMLMessage : function(message) {
            //decide, whether to show an error or a message, message only in debug mode
            if(message.indexOf("<message>") != -1 && message.indexOf("<error>") == -1) {
                var intStart = message.indexOf("<message>")+9;
                var responseText = message.substr(intStart, message.indexOf("</message>")-intStart);
                this.messageOK(responseText);
            }

            if(message.indexOf("<error>") != -1) {
                var intStart = message.indexOf("<error>")+7;
                var responseText = message.substr(intStart, message.indexOf("</error>")-intStart);
                this.messageError(responseText);
            }
        },

        /**
         * Creates a success message box contaning the passed content
         *
         * @param {String} strMessage
         */
        messageSuccess : function(strMessage) {
            toastr.success(strMessage);
        },

        /**
         * Creates a informal message box contaning the passed content
         *
         * @param {String} strMessage
         */
        messageOK : function(strMessage) {
            toastr.info(strMessage);
        },

        /**
         * Creates an error message box containg the passed content
         *
         * @param {String} strMessage
         */
        messageError : function(strMessage) {
            toastr.error(strMessage);
        },

    };

});
