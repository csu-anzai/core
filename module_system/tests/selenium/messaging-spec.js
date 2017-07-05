"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');
const SeleniumWaitHelper = requireHelper('/util/SeleniumWaitHelper.js');


describe('module_messaging', function() {

    it('test list', async function() {
        await SeleniumUtil.gotToUrl('index.php?admin=1&module=messaging&action=list');

        await element.all(by.css('.actions')).last().$('a').click();
        await SeleniumUtil.switchToModalDialog();

        // enter a new message to the form
        let inputUser = await SeleniumWaitHelper.getElementWhenPresent(by.id('messaging_user'));
        let inputTitle = await SeleniumWaitHelper.getElementWhenPresent(by.id('messaging_title'));
        let inputBody = await SeleniumWaitHelper.getElementWhenPresent(by.id('messaging_body'));
        let buttonSubmit = await SeleniumWaitHelper.getElementWhenPresent(by.css('button[type="submit"]'));

        await inputUser.sendKeys('test');

        // select user from autocomplete
        let autoCompleteEntry = await SeleniumWaitHelper.getElementWhenPresent(by.css('.ui-autocomplete .ui-menu-item'));
        await autoCompleteEntry.click();

        await inputTitle.sendKeys('foo');
        await inputBody.sendKeys('bar');
        await buttonSubmit.click();

        let elementContent = await SeleniumWaitHelper.getElementWhenPresent(by.id('content'));
        expect(elementContent.getText()).toMatch('Die Nachricht wurde erfolgreich verschickt.');

        let buttonOK = await SeleniumWaitHelper.getElementWhenPresent(by.css('button[type="submit"]'));
        await buttonOK.click();
        await browser.driver.switchTo().defaultContent();
    });

    it('provides config page', async function() {
        let mailConfigUrl = "index.php?admin=1&module=messaging&action=config";
        let enableInputLocator = By.id('Kajona-Packagemanager-System-Messageproviders-MessageproviderPackageupdate_enabled');
        let mailInputLocator = By.id('Kajona-Packagemanager-System-Messageproviders-MessageproviderPackageupdate_bymail');
        let mailButtonLocator = By.css('.bootstrap-switch-id-Kajona-Packagemanager-System-Messageproviders-MessageproviderPackageupdate_bymail');

        await SeleniumUtil.gotToUrl(mailConfigUrl);

        // check the default values

        let activeElement = await SeleniumWaitHelper.getElementWhenPresent(enableInputLocator);
        let mailElement =  await SeleniumWaitHelper.getElementWhenPresent(mailInputLocator);
        expect(activeElement.getAttribute('checked')).not.toBe(null);
        expect(mailElement.getAttribute('checked')).toBe(null);

        // click the enable button
        let mailButton = await SeleniumWaitHelper.getElementWhenPresent(mailButtonLocator);
        await mailButton.click();
        mailElement =  await SeleniumWaitHelper.getElementWhenPresent(mailInputLocator);
        expect(mailElement.getAttribute('checked')).not.toBe(null);

        // refresh
        await SeleniumUtil.gotToUrl(mailConfigUrl);

        // and revalidate if the ajax request worked as specified
        activeElement = await SeleniumWaitHelper.getElementWhenPresent(enableInputLocator);
        mailElement =  await SeleniumWaitHelper.getElementWhenPresent(mailInputLocator);
        expect(activeElement.getAttribute('checked')).not.toBe(null);
        expect(mailElement.getAttribute('checked')).not.toBe(null);
    });

});
