"use strict";

/**
 * 
 */
class SeleniumCheckboxUtil {

    /**
     *
     * Checks if the given checkbox is checked
     *
     * @param {webdriver.WebElement} elementChkBox - The checkbox element
     *
     * @returns {boolean}
     */
    static async isChecked(elementChkBox) {
        let strValue = await elementChkBox.getAttribute("checked");
        return strValue == "true";

    }

    /**
     * Checks a checkbox
     *
     * @param elementChkBox
     * @returns {void|null}
     */
    static async checkCheckbox(elementChkBox) {
        let isChecked = await SeleniumCheckboxUtil.isChecked(elementChkBox);
        if(isChecked) {
            return null;
        }
        return await elementChkBox.click();
    }

    /**
     * Unchecks a checkbox
     *
     * @param elementChkBox
     * @returns {void|null}
     */
    static async uncheckCheckbox(elementChkBox) {
        let isChecked = await SeleniumCheckboxUtil.isChecked(elementChkBox);
        if(isChecked) {
            return await elementChkBox.click();
        }

        return null;
    }
}

module.exports = SeleniumCheckboxUtil;


