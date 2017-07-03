"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');
const SeleniumWaitHelper = requireHelper('/util/SeleniumWaitHelper.js');


describe('module_messaging', function() {

    it('test list', async function() {
        await SeleniumUtil.gotToUrl('index.php?admin=1&module=messaging&action=list');

        await element.all(by.css('.actions')).last().$('a').click();

        await SeleniumUtil.switchToModalDialog();

        // enter a new message to the form
        await browser.driver.findElement(by.id('messaging_user')).sendKeys('test');

        // select user from autocomplete
        await browser.driver.wait(protractor.until.elementLocated(by.css('.ui-autocomplete .ui-menu-item')), 5000);
        await browser.driver.findElement(by.css('.ui-autocomplete .ui-menu-item')).click();

        await browser.driver.findElement(by.id('messaging_title')).sendKeys('foo');
        await browser.driver.findElement(by.id('messaging_body')).sendKeys('bar');
        await browser.driver.findElement(by.css('button[type="submit"]')).click();

        expect(browser.driver.findElement(by.id('content')).getText()).toMatch('Die Nachricht wurde erfolgreich verschickt.');

        await browser.driver.findElement(by.css('button[type="submit"]')).click();
        await browser.driver.switchTo().defaultContent();
    });

    it('provides config page', async function() {

        let mailConfigUrl = "index.php?admin=1&module=messaging&action=config";
        let enableInputLocator = By.id('Kajona-Packagemanager-System-Messageproviders-MessageproviderPackageupdate_enabled');
        let mailInputLocator = By.id('Kajona-Packagemanager-System-Messageproviders-MessageproviderPackageupdate_bymail');
        let mailButtonLocator = By.css('.bootstrap-switch-id-Kajona-Packagemanager-System-Messageproviders-MessageproviderPackageupdate_bymail');

        await SeleniumUtil.gotToUrl(mailConfigUrl);

        // check the default values
        await SeleniumWaitHelper.getElementWhenPresent(SeleniumUtil.getWebDriver(), mailInputLocator);
        expect(browser.driver.findElement(enableInputLocator).getAttribute('checked')).not.toBe(null);
        expect(browser.driver.findElement(mailInputLocator).getAttribute('checked')).toBe(null);

        await browser.driver.wait(protractor.until.elementLocated(mailButtonLocator), 5000);

        // click the enable button
        await browser.driver.findElement(mailButtonLocator).click();
        expect(browser.driver.findElement(mailInputLocator).getAttribute('checked')).not.toBe(null);

        // refresh
        await SeleniumUtil.gotToUrl(mailConfigUrl);

        // and revalidate if the ajax request worked as specified
        await SeleniumWaitHelper.getElementWhenPresent(SeleniumUtil.getWebDriver(), mailInputLocator);
        expect(browser.driver.findElement(enableInputLocator).getAttribute('checked')).not.toBe(null);
        expect(browser.driver.findElement(mailInputLocator).getAttribute('checked')).not.toBe(null);
    });

});
