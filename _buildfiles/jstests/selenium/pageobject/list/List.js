"use strict";

/**
 * require statements
 */
const MainContent = requireHelper('/pageobject/MainContent.js');
const ListRow = requireHelper('/pageobject/list/ListRow.js');
const ListBatchActionRow = requireHelper('/pageobject/list/ListBatchActionRow.js');
const ListPagination = requireHelper('/pageobject/list/ListPagination.js');


/** Constants */
const LIST = By.css(".table.admintable");
const LIST_ROWS = By.css("tbody > tr:not([data-systemid='batchActionSwitch'])");


/**
 *
 */
class List extends MainContent {

    constructor() {
        super();
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get elementList () {
        return this.webDriver.findElement(LIST);
    }

    /**
     *
     * @returns {WebElement[]}
     */
    async elementsListRows () {
        return await this.elementList.findElements(LIST_ROWS);
    }

    /**
     *
     * @returns {ListPagination}
     */
    async getPagination() {
        let listElement = await this.elementList;
        return new ListPagination(listElement);
    }


    /**
     *
     * @returns {ListRow[]}
     */
    async getArrListRows() {
        let arrElemRows = await this.elementsListRows();

        let arrListRows = [];
        for(let i = 0; i<arrElemRows.length; i++) {
            arrListRows.push(new ListRow(arrElemRows[i]));
        }
        return arrListRows;
    }


    /**
     *
     * @returns {ListBatchActionRow}
     */
    async getBatchActionRow() {
        let listElement = await this.elementList;
        return new ListBatchActionRow(listElement);
    }
}

/** @type {List} */
module.exports = List;
