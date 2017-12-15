"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');
const LoginPage = requireHelper('/pageobject/LoginPage.js');

describe('login', function () {

    it('test login', async function () {
        let page = await LoginPage.getPage();
        let adminLandingPage = await page.login("test", "test123");
    });
});
