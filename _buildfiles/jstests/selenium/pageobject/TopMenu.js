"use strict";

/**
 * require statements
 */
const BasePage = requireHelper('/pageobject/base/BasePage.js');
const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');
const SeleniumWaitHelper = requireHelper('/util/SeleniumWaitHelper.js');


/** Constants */
const SEARCHBOX_INPUT = By.xpath("//*[@id='globalSearchInput']");
const USERMENU = By.xpath("//*[@class='dropdown userNotificationsDropdown']");
const USERMENU_LOGOUT_LNK = By.xpath("//*[@class='dropdown userNotificationsDropdown']"+"/ul/li[last()]/a");

/**
 *
 */
class TopMenu extends BasePage {

    constructor() {
        super();
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_searchBox() {
        return this.webDriver.findElement(SEARCHBOX_INPUT);
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_lnkUserMenu() {
        return this.webDriver.findElement(USERMENU);
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_lnkUserMenuLogOut() {
        return this.webDriver.findElement(USERMENU_LOGOUT_LNK);
    }


    /**
     *
     * @param {string} strSearchTerm
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    async search(strSearchTerm) {
        return await this.element_searchBox.sendKeys(strSearchTerm);
    };

    /**
     * Logs out.
     *
     * @returns {Promise<void>}
     */
    async logout() {
        await this.showUserMenu();
        return await this.element_lnkUserMenuLogOut.click();
    }

    /**
     * Displays the user menu.
     *
     * @returns {webdriver.promise.Promise.<void>}
     */
    async showUserMenu() {
        let element = await this.element_lnkUserMenu;
        return await SeleniumUtil.moveToElement(element);
    }
}

/** @type {TopMenu} */
module.exports = TopMenu;
