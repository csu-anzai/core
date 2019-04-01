import Router from "./Router";
import * as $ from "jquery";
import "../jqueryui/jquery-ui.custom.min";
import "../../../module_v4skin/scripts/jquery/jquery.ui.touch-punch.min";
import "../../../module_v4skin/scripts/bootstrap/bootstrap.min";
import * as toastr from "toastr";
import V4skin from "../../../module_v4skin/scripts/kajona/V4skin";
import Loader from "./Loader";
import Dialog from "../../../module_v4skin/scripts/kajona/Dialog";
import Folderview from "./Folderview";
import Lists from "./Lists";
import DialogHelper from "../../../module_v4skin/scripts/kajona/DialogHelper";
import Ajax from "./Ajax";
import * as moment from "moment";
const mermaid = require("mermaid");
// import mermaid from 'mermaid'

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
          dialog: new Dialog("folderviewDialog", 0)
        },
        lang: {},
        forms: {
          submittedEl: null,
          monitoredEl: null
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
    window.jsDialog_0 = new Dialog("jsDialog_0", 0);
    /** @deprecated */
    window.jsDialog_1 = new Dialog("jsDialog_1", 1);
    /** @deprecated */
    window.jsDialog_2 = new Dialog("jsDialog_2", 2);
    /** @deprecated */
    window.jsDialog_3 = new Dialog("jsDialog_3", 3);

    // configure toastr global
    toastr.options.positionClass = "toast-bottom-right";
  }
}

//register all the global dependencies in window object
(<any>window).App = App;
(<any>window).$ = (<any>window).jQuery = require("jquery");
(<any>window).moment = moment;
(<any>window).mermaid = mermaid;
export default App;
