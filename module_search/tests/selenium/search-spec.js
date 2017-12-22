"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');

describe('module_search', function() {

    it('test list', async function() {
        await SeleniumUtil.gotToUrl('index.php?admin=1&module=search&action=list');
    });

});
