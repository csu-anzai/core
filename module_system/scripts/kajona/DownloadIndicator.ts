///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="downloadIndicator"/>

import dh = require("./../../../module_v4skin/scripts/kajona/DialogHelper");
import wi = require("./WorkingIndicator");

/**
 * The downloadIndicator may be used when starting a complex backend operation the browser needs to wait for.
 * Therefor an indicator is shown and being closed by the backend using a cookie
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
 */
class DownloadIndicator {

    private key: string;
    private cookieName: string;
    private attempts: number;
    private intervalHandle: any;


    /**
     * Generates a key to be used by the backend in order to set a cookie indicating the end of the
     * progress
     */
    public generateKey(): string {
        if (this.key == null) {
            this.key = new Date().getTime()+"";
            this.cookieName = "kj_"+new Date().getTime();
        }
        return this.key;
    };

    /**
     * Starts the "in progress" animation
     */
    public setWorking(): void {
        dh.showLoadingModal();
        wi.getInstance().start();

        let outer = this;
        this.intervalHandle = window.setInterval(function() {
            outer.checkFinished();
        }, 200);
    };

    /**
     * Validates if the current request was finished on the backend
     */
    public checkFinished(): void {
        let token = this.getCookie();

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
    private stopWorking(): void {
        window.clearInterval(this.intervalHandle);
        this.expireCookie();
        dh.hideLoadingModal();
        wi.getInstance().stop();
    };

    /**
     * Internal helper to get the cookie value
     * @returns {string}
     */
    private getCookie(): string {
        let parts = document.cookie.split(this.cookieName + "=");
        if (parts.length == 2)
            return parts.pop().split(";").shift();
    };

    /**
     * Invalidates the cookie
     */
    private expireCookie() {
        document.cookie = encodeURIComponent(this.cookieName) + "=deleted; expires=" + new Date( 0 ).toUTCString();
    }

}

class DownloadIndicatorPublic  {
    /**
     * Returns a new indicator
     * @returns {DownloadIndicator}
     */
    public static getIndicator() {
        return new DownloadIndicator();
    };

    /**
     * The way to go, pass a url to be loaded.
     * A new manager is being created and takes care of the processing afterwards
     * @param downloadUrl
     */
    public static triggerDownload(downloadUrl: string) {
        let manager = new DownloadIndicator();
        let key = manager.generateKey();
        manager.setWorking();

        if (downloadUrl.indexOf('?') == -1) {
            downloadUrl = downloadUrl+"?indicatorToken="+key;
        } else {
            downloadUrl = downloadUrl+"&indicatorToken="+key;
        }

        document.location.href = downloadUrl;
    }
}

export = DownloadIndicatorPublic;