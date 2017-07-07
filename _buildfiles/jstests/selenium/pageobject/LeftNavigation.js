"use strict";

/**
 * require statements
 */
const BasePage = requireHelper('/pageobject/base/BasePage.js');
const SeleniumWaitHelper = requireHelper('/util/SeleniumWaitHelper.js');

/** Constants */
const LEFTNAVIGATION_XPATH_NAVIGATION = ".//*[@id='moduleNavigation']";

const NAVIGATION = By.xpath(LEFTNAVIGATION_XPATH_NAVIGATION);
const NAVIGATION_HAMBURGER = By.xpath(".//*[@data-toggle='offcanvas']");//visible when page width < 932px


/**
 *
 */
class LeftNavigation extends BasePage {

    constructor() {
        super();
    }


    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_navigation() {
        return this.webDriver.findElement(NAVIGATION);
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_navigationHamburger() {
        return this.webDriver.findElement(NAVIGATION_HAMBURGER);
    }

    /**
     * Opens the navigation
     *
     * @returns {*}
     */
    async showNavigation() {
        let isMenuDisplayed = await this.isNavigationDisplayed();

        //if menu is not displayed, check if there is a hamburger element and click it
        if(!isMenuDisplayed) {
            let isHamburgerMenuVisible = await this.isNavigationHamburgerDisplayed();
            if(isHamburgerMenuVisible) {
                await this.element_navigationHamburger.click();
            }
        }
    }

    /**
     * Checks if the hamburger element to open/close the navigation is present
     *
     * @returns {boolean}
     */
    async isNavigationHamburgerDisplayed() {
        return await this.element_navigationHamburger.isDisplayed();
    }

    /**
     * Checks if the navigation is displayed
     *
     * @returns {boolean}
     */
    async isNavigationDisplayed() {
        let strPath = LEFTNAVIGATION_XPATH_NAVIGATION + "/../../..";

        //if element has class active -> menu is displayed
        let strValue = await this.webDriver.findElement(By.xpath(strPath)).getAttribute("class");
        return strValue.indexOf("active") !== -1
    }


    /**
     * Gets a module from the navigation with the given name
     *
     * @param {string} strMenuName
     * @returns {webdriver.WebElement}
     */
    async getNavigationModule(strMenuName) {
        let strPathMenu = LEFTNAVIGATION_XPATH_NAVIGATION + "//*[contains(concat(' ', @class, ' '), ' panel-heading ')]/a[contains(text(), '" + strMenuName + "')]";
        return await SeleniumWaitHelper.getElementWhenDisplayed(By.xpath(strPathMenu));
    }

    /**
     * Checks if the module in the navigation is already opened
     *
     * @param {string} strMenuName
     * @returns {Promise<boolean>}
     */
    async isNavigationModuleOpened(strMenuName) {
        let menuElement = await this.getNavigationModule(strMenuName);

        let strValueclass = await menuElement.getAttribute("class");

        if (strValueclass === null || strValueclass.indexOf("collapsed") > -1) {
            return false;
        }
        else if (strValueclass === "") {
            let strValueAria = await menuElement.getAttribute("aria-expanded");
            return (strValueAria === "true");
        }

        return true;
    }

    /**
     * Opens a module in the navigation
     *
     * @param {string} strMenuName
     * @returns {*}
     */
    async openNavigationModule(strMenuName) {
        await this.showNavigation();

        let isModuleMenuOpened = await this.isNavigationModuleOpened(strMenuName);
        if(!isModuleMenuOpened) {
            let menuElement = await this.getNavigationModule(strMenuName);
            menuElement.click();
        }
    };


    /**
     * Gets a links for a navigation module
     *
     * @param {string} strMenuName
     * @returns {*}
     */
    async getNavigationModuleLinks(strMenuName) {
        await this.openNavigationModule(strMenuName);

        let strPathToLinks = LEFTNAVIGATION_XPATH_NAVIGATION + "//*[contains(concat(' ', @class, ' '), ' panel-heading ')]/a[contains(text(), '" + strMenuName + "')]/../..//li[a[contains(concat(' ', @class, ' '), ' adminnavi ')]]";
        return await SeleniumWaitHelper.getElementsWhenPresent(By.xpath(strPathToLinks));
    };


    /**
     * Gets a single link from a navigation module
     * @param {string} strMenuName
     * @param {integer} intLinkPosition
     * @returns {*}
     */
    async getModuleMenuLink(strMenuName, intLinkPosition) {
        await this.openNavigationModule(strMenuName);

        let strPathToLinks = LEFTNAVIGATION_XPATH_NAVIGATION + "//*[contains(concat(' ', @class, ' '), ' panel-heading ')]/a[contains(text(), '" + strMenuName + "')]/../..//li[a[contains(concat(' ', @class, ' '), ' adminnavi ')]][" + intLinkPosition + "]";
        return await SeleniumWaitHelper.getElementWhenDisplayed(By.xpath(strPathToLinks));
    }
}


/** @type {LeftNavigation} */
module.exports = LeftNavigation;
