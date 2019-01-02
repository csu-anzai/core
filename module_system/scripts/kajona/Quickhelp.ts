///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="quickhelp"/>

import * as $ from "jquery";
import "bootstrap";

/**
 * Module to handle the general quickhelp entry
 */
class Quickhelp {

    public static setQuickhelp(strTitle: string, strText: string) {
        if(strText.trim() == "" ) {
            return;
        }
        $('#quickhelp').popover({
            title: strTitle,
            content: strText,
            placement: 'bottom',
            trigger: 'hover',
            html: true
        }).css("cursor", "help").show();

    };

    public static resetQuickhelp() {
        $('#quickhelp').hide().popover('destroy');
    };

}

export = Quickhelp;
