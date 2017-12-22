"use strict";

/**
 * require statements
 */
const BasePage = requireHelper('/pageobject/base/BasePage.js');

/** Constants */
const BATCHACTIONROW = By.css("tbody > tr[data-systemid='batchActionSwitch']");

/**
 *
 */
class ListBatchActionRow extends BasePage {

    /**
     * 
     * @param {WebElementPromise} elementList
     */
    constructor(elementList) {
        super();

        this._elementList = elementList;
    }

    get elementBatchActionRow() {
        return this._elementList.findElement(BATCHACTIONROW);
    }

    /**
     * Presses the (+) Button at the end of a list
     *
     * @returns {void}
     */
    async createObject() {
        return await this.elementBatchActionRow.findElement(By.css("td.actions a")).click();
    }
}

/** @type {ListBatchActionRow} */
module.exports = ListBatchActionRow;
