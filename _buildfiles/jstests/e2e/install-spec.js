"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');

describe('installation', function() {

    it('test installation', async function() {
        let intTimeout = 100000 * 10;

        // wait max 5 minutes for the installation
        browser.manage().timeouts().pageLoadTimeout(intTimeout);

        await SeleniumUtil.gotToUrl('installer.php');

        let webDriver = SeleniumUtil.getWebDriver();

        await webDriver.findElement(By.css('.btn-primary')).click();

        // db settings
        await webDriver.findElement(By.id('hostname')).sendKeys('localhost');
        await webDriver.findElement(By.id('username')).sendKeys('kajona');
        await webDriver.findElement(By.id('password')).sendKeys('kajona');
        await webDriver.findElement(By.id('dbname')).sendKeys('autotest');
        // default is "kajona_"
        //webDriver.findElement(by.id('dbprefix')).sendKeys('');
        await webDriver.findElement(By.css('option[value="sqlite3"]')).click();

        await webDriver.findElement(By.css('.savechanges')).click();

        // create new admin user
        await webDriver.findElement(By.id('username')).sendKeys('test');
        await webDriver.findElement(By.id('password')).sendKeys('test123');
        await webDriver.findElement(By.id('email')).sendKeys('test@test.com');

        await webDriver.findElement(By.css('.savechanges')).click();

        // start the installation this takes some time
        await webDriver.findElement(By.css('.savechanges')).click();

        // wait for the installation
        await webDriver.wait(function() {
            return webDriver.getCurrentUrl().then(function(url) {
                return /finish/.test(url);
            });
        }, intTimeout);

        // now we must have a success message
        expect(webDriver.findElement(By.css('.alert-success')).getText()).toMatch('Herzlichen Glückwunsch!');
    });
});
