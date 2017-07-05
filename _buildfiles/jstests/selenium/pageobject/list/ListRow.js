"use strict";

/** require statements */
const BasePage = requireHelper('/pageobject/base/BasePage.js');

/** Constants */
const ACTIONICONS = By.css("td.actions span.listButton");

/**
 *
 */
class ListRow extends BasePage {

    /**
     *
     * @param {WebElementPromise} elementRow
     */
    constructor(elementRow) {
        super();

        this._elementRow = elementRow;
    }


    /**
     * Returns all action icons of the row
     *
     * @return {WebElement[]}
     */
    async getArrActionIcons() {
        return await this._elementRow.findElements(ACTIONICONS);
    }

}

/** @type {ListRow} */
module.exports = ListRow;
