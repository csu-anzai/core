///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="app"/>

import Router = require("./Router");
import * as $ from "jquery";
import "jquery-ui";
import "jquery-touchPunch";
import "bootstrap";
import * as toastr from "toastr";
import V4skin = require("../../../module_v4skin/scripts/kajona/V4skin");
import Loader = require("./Loader");
import Dialog = require("../../../module_v4skin/scripts/kajona/Dialog");
import Folderview = require("./Folderview");
import Lists = require("./Lists");
import DialogHelper = require("../../../module_v4skin/scripts/kajona/DialogHelper");
import Ajax = require("./Ajax");

declare global {
    interface Window {
        KAJONA: Kajona;
        jsDialog_0: Dialog;
        jsDialog_1: Dialog;
        jsDialog_2: Dialog;
        jsDialog_3: Dialog;
    }
}

class App {

    public static init() {

        //backwards compatibility
        window.KAJONA = {
            util: {
                dialogHelper: DialogHelper,
                folderviewHandler: null
            },
            portal: {
                lang: {}
            },
            admin: {
                folderview: {
                    dialog: new Dialog('folderviewDialog', 0)
                },
                lang: {},
                forms: {
                    submittedEl: null,
                    monitoredEl: null,
                }
            }
        };

        Folderview.dialog = KAJONA.admin.folderview.dialog;

        //register the global router
        Router.init();

        // V4skin
        V4skin.initCatComplete();
        V4skin.initPopover();
        V4skin.initScroll();
        V4skin.initBreadcrumb();
        V4skin.initMenu();
        V4skin.initTopNavigation();

        // BC layer

        /** @deprecated */
        window.jsDialog_0 = new Dialog('jsDialog_0', 0);
        /** @deprecated */
        window.jsDialog_1 = new Dialog('jsDialog_1', 1);
        /** @deprecated */
        window.jsDialog_2 = new Dialog('jsDialog_2', 2);
        /** @deprecated */
        window.jsDialog_3 = new Dialog('jsDialog_3', 3);

        // configure toastr global
        toastr.options.positionClass = "toast-bottom-right";
    }

}

export = App;
