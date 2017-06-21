"use strict";

/**
 * 
 */
class SeleniumSelectboxUtil {


    /**
     *
     * Checks if the given value is selected in the select box
     *
     * @param {webdriver.WebElement} elementSelectBox - The selectbox element
     * @param {string} strValue - The checkbox element
     *
     * @returns {webdriver.promise.Promise<boolean>}
     */
    static async isValueSelected(elementSelectBox, strValue) {
        let value = await elementSelectBox.getAttribute("value");
        return strValue == value;
    }

    /**
     * Selects a value in the select box
     *
     * @param {webdriver.WebElement} elementSelectBox - The selectbox element
     * @param {string} strValue - The checkbox element
     *
     * @returns {void|null}
     */
    static async selectByValue(elementSelectBox, strValue) {
        let strCss = "option[value='"+strValue+"']";//e.g. option[value='5']
        return await elementSelectBox.findElement(By.css(strCss)).click();
    }

    /**
     * Selects an option in the select box by the given options element
     *
     * @param {webdriver.WebElement} elementSelectBox - The selectbox element
     * @param {webdriver.WebElement}  elementOption - The checkbox element
     *
     * @returns {void}
     */
    static async selectByElementOption(elementSelectBox, elementOption) {
        return await elementOption.click();
    }
}

module.exports = SeleniumSelectboxUtil;


