///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="dashboard"/>

import * as $ from "jquery";
import "jquery-ui";
import Ajax = require("../../../module_system/scripts/kajona/Ajax");
import Tooltip = require("../../../module_system/scripts/kajona/Tooltip");
import StatusDisplay = require("../../../module_system/scripts/kajona/StatusDisplay");
import Util = require("../../../module_system/scripts/kajona/Util");

class Todo {

    public static selectedCategory: string = "";

    public static loadCategory(category: string, search: any){
        if (search == '') {
            $('#listfilter_search').val('');
        }
        this.selectedCategory = category;
        $('#todo-table').html('<div class="loadingContainer"></div>');
        Ajax.genericAjaxCall('dashboard', 'todoCategory', '&category=' + category + '&search=' + search, function(data: any) {
            $('#todo-table').html(data);
            Tooltip.initTooltip();
        });
    }

    public static formSearch(){
        this.loadCategory(this.selectedCategory, $('#listfilter_search').val());
    }

}

class Dashboard {

    public static todo : Todo = Todo;

    public static removeWidget(strSystemid: string) {
        Ajax.genericAjaxCall('dashboard', 'deleteWidget', strSystemid, function(data: any, status: string, jqXHR: XMLHttpRequest) {
            if (status == 'success') {

                $("div[data-systemid="+strSystemid+"]").remove();
                StatusDisplay.displayXMLMessage(data);
                jsDialog_1.hide();

            } else {
                StatusDisplay.messageError('<b>Request failed!</b><br />' + data);
            }
        });
    }

    public static init() {

        $('.adminwidgetColumn > div.core-component-widget').each(function () {
            let systemId = $(this).data('systemid');
            Ajax.loadUrlToElement("div.core-component-widget[data-systemid='"+systemId+"'] .content", "/xml.php?admin=1&module=dashboard&action=getWidgetContent&systemid="+systemId);
        });

        $("div.adminwidgetColumn").each(function(index: number) {

            $(this).sortable({
                items: 'div.core-component-widget',
                handle: 'h2',
                forcePlaceholderSize: true,
                cursor: 'move',
                connectWith: '.adminwidgetColumn',
                placeholder: 'dndPlaceholder',
                stop: function(event: any, ui: any) {
                    ui.item.removeClass("sortActive");
                    //search list for new pos
                    let intPos = 0;
                    $(".dbEntry").each(function(index: number) {
                        intPos++;
                        if($(this).data("systemid") == ui.item.data("systemid")) {
                            Ajax.genericAjaxCall("dashboard", "setDashboardPosition", ui.item.data("systemid") + "&listPos=" + intPos+"&listId="+ui.item.closest('div.adminwidgetColumn').attr('id'), Ajax.regularCallback);
                            return false;
                        }
                    });
                },
                delay: Util.isTouchDevice() ? 500 : 0,
                start: function(event: any, ui: any) {
                    ui.item.addClass("sortActive");
                }
            }).find("h2").css("cursor", "move");
        });
    }

    public static editWidget (strSystemid: string) {
        Ajax.loadUrlToElement("div.core-component-widget[data-systemid='"+strSystemid+"'] .content", "/xml.php?admin=1&module=dashboard&action=switchOnEditMode&systemid="+strSystemid);
    }

    public static updateWidget (form: string, strSystemid: string) {
        let data = $(form).serialize();
        Ajax.loadUrlToElement("div.core-component-widget[data-systemid='"+strSystemid+"'] .content", "/xml.php?admin=1&module=dashboard&action=updateWidgetContent&systemid="+strSystemid+"&"+data);
    }
}

export = Dashboard;
