/********************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

/**
 * The downloadIndicator may be used when starting a complex backend operation the browser needs to wait for.
 * Therefore an indicator is shown and being closed by the backend using a cookie
 *
 * Frontend:
 * <code>
 * require(['downloadIndicator'], function(dl) {
 *   dl.triggerDownload(KAJONA_WEBPATH+"/xml.php?module=system&action=downloadTest");
 * });
 * </code>
 *
 * Backend:
 * <code>
 *     ResponseObject::getInstance()->handleProgressCookie();
 * </code>
 *
 * @module downloadIndicator
 */
define("downloadIndicator", ['jquery', 'dialogHelper', 'workingIndicator'], function ($, dh, wi) {

    /**
     * Internal model for a single download indicator
     * @constructor
     */
    var DownloadIndicator = function() {

        this.key = null;
        this.cookieName = null;
        this.attempts = 0;
        this.intervalHandle = null;

        /**
         * Generates a key to be used by the backend in order to set a cookie indicating the end of the
         * progress
         * @returns {null|*}
         */
        this.generateKey = function() {
            if (this.key == null) {
                this.key = new Date().getTime();
                this.cookieName = "kj_"+new Date().getTime();
            }
            return this.key;
        };

        /**
         * Starts the "in progress" animation
         */
        this.setWorking = function() {
            dh.showLoadingModal();
            wi.getInstance().start();

            var checker = new ProgressChecker(this);
            this.intervalHandle = window.setInterval(checker.handleCheck, 200);
        };

        /**
         * Validates if the current request was finished on the backend
         */
        this.checkFinished = function() {
            var token = this.getCookie();

            if(token == this.key ) {
                this.stopWorking();
            }

            if (this.attempts++ > 500) {
                this.stopWorking();
            }
        };

        /**
         * Stops all working animation
         */
        this.stopWorking = function () {
            window.clearInterval(this.intervalHandle);
            this.expireCookie();
            dh.hideLoadingModal();
            wi.getInstance().stop();
        };

        /**
         * Internal helper to get the cookie value
         * @returns {string}
         */
        this.getCookie = function() {
            var parts = document.cookie.split(this.cookieName + "=");
            if (parts.length == 2)
                return parts.pop().split(";").shift();
        };

        /**
         * Invalidates the cookie
         */
        this.expireCookie = function() {
            document.cookie = encodeURIComponent(this.cookieName) + "=deleted; expires=" + new Date( 0 ).toUTCString();
        }

    };


    /**
     * A simple value holder to be passed to the browsers setInterval and to keep references
     * @param indicator
     * @constructor
     */
    var ProgressChecker = function(indicator) {
        /**
         * @var DownloadIndicator
         */
        var downloadIndicator = indicator;

        this.handleCheck = function() {
            downloadIndicator.checkFinished();
        }

    };

    return {
        /**
         * Returns a new indicator
         * @returns {DownloadIndicator}
         */
        getIndicator : function() {
            return new DownloadIndicator();
        },

        /**
         * The way to go, pass a url to be loaded.
         * A new manager is being created and takes care of the processing afterwards
         * @param downloadUrl
         */
        triggerDownload : function (downloadUrl) {
            var manager = new DownloadIndicator();
            var key = manager.generateKey();
            manager.setWorking();

            if (downloadUrl.indexOf('?') == -1) {
                downloadUrl = downloadUrl+"?indicatorToken="+key;
            } else {
                downloadUrl = downloadUrl+"&indicatorToken="+key;
            }

            document.location.href = downloadUrl;
        }
    }
});
