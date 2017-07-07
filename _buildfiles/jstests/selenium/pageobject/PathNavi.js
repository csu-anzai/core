"use strict";

/**
 * require statements
 */
const BasePage = requireHelper('/pageobject/base/BasePage.js');



/** Constants */
const PATHCONTAINER = By.css("div.pathNaviContainer");
const BREADCRUMP = By.css("ul.breadcrumb");

/**
 *
 */
class PathNavi extends BasePage {

    /**
     *
     */
    constructor() {
        super();
    }

    /**
     *
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get elemPathNavi() {
        return this.webDriver.findElement(PATHCONTAINER);
    }

    /**
     * @returns {WebElementPromise|!webdriver.WebElement}
     */
    get element_breadCrumb() {
        this.elemPathNavi.findElement(BREADCRUMP);
    }

}

/** @type {PathNavi} */
module.exports = PathNavi;
