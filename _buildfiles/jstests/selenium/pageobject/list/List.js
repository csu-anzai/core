"use strict";

/**
 * require statements
 */
const MainContent = requireHelper('/pageobject/MainContent.js');
const ListRow = requireHelper('/pageobject/list/ListRow.js');
const ListBatchActionRow = requireHelper('/pageobject/list/ListBatchActionRow.js');
const ListPagination = requireHelper('/pageobject/list/ListPagination.js');


/** Constants */
const LIST = by.css(".table.admintable");
const LIST_ROWS = by.css("tbody > tr:not([data-systemid='batchActionSwitch'])");


/**
 *
 */
class List extends MainContent {

    constructor() {
        super();

        /** @type {webdriver.promise.Promise<ListRow[]>} */
        this._arrListRows = this._createListRows();

        /** @type {ListBatchActionRow} */
        this._listBatchActionRow = new ListBatchActionRow(this.elementList);

        /** @type {ListPagination} */
        this._listPagination = new ListPagination(this.elementList);
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
        return this._listPagination;
    }


    /**
     *
     * @returns {ListRow[]}
     */
    async getArrListRows() {
        return this._arrListRows;
    }


    /**
     *
     * @returns {ListBatchActionRow}
     */
    async getBatchActionRow() {
        return this._listBatchActionRow;
    }

    /**
     *
     * @returns {ListRow[]}
     */
    async _createListRows() {
        let arrElemRows = await this.elementsListRows();

        let arrListRows = [];
        for(let i = 0; i<arrElemRows.length; i++) {
            arrListRows.push(new ListRow(arrElemRows[i]));
        }
        return arrListRows;
    }
}

/** @type {List} */
module.exports = List;
