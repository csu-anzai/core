"use strict";


/**
 *
 */
class SeleniumWaitHelper {

    /**
     * Returns an elements when it is displayed.
     * This is a blocking wait.
     *
     * @param {webdriver.WebDriver} webDriver
     * @param locator
     * @returns {!webdriver.WebElement}
     */
    static async getElementWhenDisplayed(webDriver, locator) {
        await webDriver.wait(
            protractor.until.elementIsVisible(webDriver.findElement(locator), 10000)
        );

        return webDriver.findElement(locator);
    }

    /**
     * Checks if an element is displayed
     * This is a blocking wait.
     *
     * @param {webdriver.WebDriver} webDriver
     * @param locator
     * @returns {bool}
     */
    static async isElementDisplayed(webDriver, locator) {
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
     * @param {webdriver.WebDriver} webDriver
     * @param locator
     * @returns {WebElement[]}
     */
    static async getElementsWhenPresent(webDriver, locator) {
        await webDriver.wait(
            protractor.until.elementsLocated(locator, 10000)
        );

        return await webDriver.findElements(locator);
    }

    /**
     * Returns an element when it is present in the DOM.
     * This is a blocking wait.
     *
     * @param webDriver
     * @param locator
     * @returns {!webdriver.WebElement}
     */
    static async getElementWhenPresent(webDriver, locator) {
        await webDriver.wait(
            protractor.until.elementLocated(locator, 10000)
        );

        return await webDriver.findElement(locator);
    }

    /**
     * Checks if an element exists.
     *
     * @param webDriver
     * @param locator
     * @returns {bool}
     */
    static async isElementExists(webDriver, locator) {
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
