"use strict";

const SeleniumUtil = requireHelper('/util/SeleniumUtil.js');

describe('module_pages', function() {

    it('test list', function() {
        SeleniumUtil.gotToUrl('index.php?admin=1&module=pages&action=list');
    });

});
