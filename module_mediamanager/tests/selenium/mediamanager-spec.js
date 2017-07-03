"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');

describe('module_mediamanager', function() {

    it('test list', async function() {
        await SeleniumUtil.gotToUrl('index.php?admin=1&module=mediamanager&action=list');
    });

});
