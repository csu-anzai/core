
require(['jquery', 'jquery-ui', 'jquery-touchPunch', 'bootstrap', 'v4skin', 'loader', 'dialog', 'folderview', 'lists', 'dialogHelper'], function(jquery, jqueryui, touch, bootstrap, v4skin, loader, Dialog, folderview) {

    //backwards compatibility
    if (typeof KAJONA == "undefined") {
        KAJONA = {
            util: {},
            portal: {
                lang: {}
            },
            admin: {
                folderview: {},
                lang: {},
                forms: {}
            }
        };
    }

    KAJONA.admin.folderview.dialog = new Dialog('folderviewDialog', 0);
    folderview.dialog = KAJONA.admin.folderview.dialog;

    $ = jquery;

    // BC layer

    /** @deprecated */
    jsDialog_0 = new Dialog('jsDialog_0', 0);
    /** @deprecated */
    jsDialog_1 = new Dialog('jsDialog_1', 1);
    /** @deprecated */
    jsDialog_2 = new Dialog('jsDialog_2', 2);
    /** @deprecated */
    jsDialog_3 = new Dialog('jsDialog_3', 3);
});
