"use strict";

const LOGINCONTAINER = By.id("loginContainer");
const FOLDERVIEW_IFRAME = By.id('folderviewDialog_iframe');

/**
 * 
 */
class SeleniumUtil {

    /**
     *
     * Moves the mouse to the given element
     *
     * @param {webdriver.WebElement} element - The Element to which should be moved to
     *
     * @returns {void}
     */
    static async moveToElement(element) {
        return await SeleniumUtil.getWebDriver().actions().mouseMove(element).perform();
    };

    /**
     * Gets the Base url
     *
     * @returns {string}
     */
    static getBaseUrl() {
        return browser.baseUrl;
    };

    /**
     *
     * @param strUrl
     * @returns {void}
     */
    static async gotToUrl(strUrl) {
        await SeleniumUtil.getWebDriver().get(SeleniumUtil.getBaseUrl()+"/"+strUrl);
    };

    /**
     * Gets the current webdriver instance
     *
     * @returns {webdriver.WebDriver}
     */
    static getWebDriver() {
        return browser.driver;
    };

    /**
     * If user is logged in, this method logs out the user
     * If user is not logged in, this method logs in the user
     *
     * @param strUserName
     * @param strPassword
     *
     * @returns {webdriver.promise.Promise<void>}
     */
    static async loginOrLogout(strUserName, strPassword) {

        const SeleniumWaitHelper = requireHelper('/util/SeleniumWaitHelper.js');
        const LoginPage = requireHelper('/pageobject/LoginPage.js');
        const AdminLandingPage = requireHelper('/pageobject/AdminLandingPage.js');

        let loginPage = await LoginPage.getPage();

        //if login container is present => login
        let bitLoginContainerIsPresent = await SeleniumWaitHelper.isElementDisplayed(LOGINCONTAINER);
        if(bitLoginContainerIsPresent) {
            return await loginPage.login(strUserName, strPassword);
        }

        //else logout user
        let adminlandingPage = await AdminLandingPage.getPage();
        return await adminlandingPage.topMenu.logout();
    };

    /**
     * Switch to the modal dialog
     */
    static async switchToModalDialog() {
        await browser.driver.wait(protractor.until.elementLocated(FOLDERVIEW_IFRAME), 5000);
        await browser.driver.switchTo().frame(browser.driver.findElement(FOLDERVIEW_IFRAME));
    }
}

module.exports = SeleniumUtil;


