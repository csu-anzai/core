"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');

/**
 *
 */
class SeleniumWaitHelper {

    /**
     * Returns an elements when it is displayed.
     * This is a blocking wait.
     *
     * @param locator
     * @returns {!webdriver.WebElement}
     */
    static async getElementWhenDisplayed(locator) {
        const webDriver = SeleniumUtil.getWebDriver();

        await webDriver.wait(
            protractor.until.elementIsVisible(webDriver.findElement(locator), 10000)
        );

        return webDriver.findElement(locator);
    }

    /**
     * Checks if an element is displayed
     * This is a blocking wait.
     *
     * @param locator
     * @returns {bool}
     */
    static async isElementDisplayed(locator) {
        const webDriver = SeleniumUtil.getWebDriver();

        await webDriver.wait(
            protractor.until.elementIsVisible(webDriver.findElement(locator), 10000)
        );

        return await webDriver.findElement(locator).then(
            function (element) {
                return element.isDisplayed();
            },
            function(err) {
                return false;
            })
    }

    /**
     * Returns all elements when it is present in the DOM.
     * This is a blocking wait.
     *
     * @param locator
     * @returns {WebElement[]}
     */
    static async getElementsWhenPresent(locator) {
        const webDriver = SeleniumUtil.getWebDriver();

        await webDriver.wait(
            protractor.until.elementsLocated(locator, 10000)
        );

        return await webDriver.findElements(locator);
    }

    /**
     * Returns an element when it is present in the DOM.
     * This is a blocking wait.
     *
     * @param locator
     * @returns {!webdriver.WebElement}
     */
    static async getElementWhenPresent(locator) {
        const webDriver = SeleniumUtil.getWebDriver();

        await webDriver.wait(
            protractor.until.elementLocated(locator, 10000)
        );

        return await webDriver.findElement(locator);
    }

    /**
     * Checks if an element exists.
     *
     * @param locator
     * @returns {bool}
     */
    static async isElementExists(locator) {
        const webDriver = SeleniumUtil.getWebDriver();

        return await webDriver.findElement(locator).then(
            function (element) {
                return true;
            },
            function(err) {
                return false;
            })
    }
}

module.exports = SeleniumWaitHelper;
