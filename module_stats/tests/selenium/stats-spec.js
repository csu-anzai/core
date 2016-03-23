
describe('module_stats', function() {

    beforeEach(function() {
        browser.ignoreSynchronization = true;
    });

    it('test list', function() {
        browser.get('index.php?admin=1&module=stats&action=list');

        expect(browser.driver.findElement(by.id('moduleTitle')).getText()).toEqual('Statistiken');
    });

});