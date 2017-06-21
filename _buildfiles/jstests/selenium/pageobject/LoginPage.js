"use strict";

/**
 * require statements
 */
const SeleniumWaitHelper = requireHelper('/util/SeleniumWaitHelper.js');
const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');
const BasePage = requireHelper('/pageobject/base/BasePage.js');
const AdminLandingPage = requireHelper('/pageobject/AdminLandingPage.js');


/** Constants */
const USERNAME = by.id("name");
const PASSWORD = by.id("passwort");
const LOGINBUTTON = by.css("button");

/**
 *
 */
class LoginPage extends BasePage {

    constructor() {
        super();
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_userName() {
        return this.webDriver.findElement(USERNAME);
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_password() {
        return this.webDriver.findElement(PASSWORD);
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_loginBtn() {
        return this.webDriver.findElement(LOGINBUTTON);
    }

    /**
     *
     * @returns {LoginPage}
     */
    static async getPage() {
        await SeleniumUtil.gotToUrl("index.php?admin=1");
        return new LoginPage();
    }

    /**
     * Logins the user.
     * 
     * @param {string} strUserName
     * @param {string} strPassword
     * @returns {AdminLandingPage}
     */
    async login(strUserName, strPassword) {

        await this.element_userName.sendKeys(strUserName);
        await this.element_password.sendKeys(strPassword);
        await this.element_loginBtn.click();

        return new AdminLandingPage();
    };
}

/** @type {LoginPage} */
module.exports = LoginPage;
