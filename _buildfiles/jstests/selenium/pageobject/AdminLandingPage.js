"use strict";

/**
 * require statements
 */
const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');
const AdminBasePage = requireHelper('/pageobject/base/AdminBasePage.js');

/**
 * 
 */
class AdminLandingPage extends AdminBasePage {

    constructor() {
        super();
    }

    /**
     *
     * @returns {AdminLandingPage}
     */
    static async getPage() {
        await SeleniumUtil.gotToUrl("index.php?admin=1");
        return new AdminLandingPage();
    }
}

/** @type {AdminLandingPage} */
module.exports = AdminLandingPage;
