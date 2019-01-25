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

        $('.adminwidgetColumn > div.dbEntry').each(function () {
            var systemId = $(this).data('systemid');
            Ajax.genericAjaxCall('dashboard', 'getWidgetContent', systemId, function(data: any, status: string, jqXHR: XMLHttpRequest) {

                var content = $("div.dbEntry[data-systemid='"+systemId+"'] .content");

                if (status == 'success') {
                    var $parent = content.parent();
                    content.remove();

                    var $newNode = $("<div class='content loaded'></div>").append($.parseJSON(data));
                    $parent.append($newNode);

                    //TODO use jquerys eval?
                    Util.evalScript(data);
                    Tooltip.initTooltip();

                } else {
                    //statusDisplay.messageError('<b>Request failed!</b><br />' + data);
                }
            });
        });

        $("div.adminwidgetColumn").each(function(index: number) {

            $(this).sortable({
                items: 'div.dbEntry',
                handle: 'h2',
                forcePlaceholderSize: true,
                cursor: 'move',
                connectWith: '.adminwidgetColumn',
                placeholder: 'dndPlaceholder',
                stop: function(event: any, ui: any) {
                    ui.item.removeClass("sortActive");
                    //search list for new pos
                    var intPos = 0;
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
}

export = Dashboard;