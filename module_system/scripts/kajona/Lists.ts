///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="lists"/>

import * as $ from "jquery";
import lang = require("./Lang");
import utils = require("./Util");

class Lists {

    private static arrSystemids : Array<string> = [];
    private static strConfirm : string = '';
    private static strCurrentUrl : string = '';
    private static strCurrentTitle : string = '';
    private static strDialogTitle : string = '';
    private static strDialogStart : string = '';
    private static intTotal : number = 0;
    private static bitRenderInfo : boolean;

    /**
     * Toggles all fields
     */
    public static toggleAllFields() {
        //batchActionSwitch
        $("table.admintable input[type='checkbox']").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                $(this).prop("checked", $('#kj_cb_batchActionSwitch').prop("checked"));
            }
        });
    };


    /**
     * Toggles all fields with the given system id's
     *
     * @param arrSystemIds
     */
    public static toggleFields(arrSystemIds: Array<string>) {
        //batchActionSwitch
        $("table.admintable input[type='checkbox']").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                var strSysid = $(this).closest("tr").data('systemid');
                if($.inArray(strSysid, arrSystemIds) !== -1) {//if strId in array
                    $(this).prop("checked", !$(this).prop("checked"));
                }
            }
        });
        this.updateToolbar();
    };


    public static updateToolbar() {
        if($("table.admintable  input:checked").length == 0) {
            $('.batchActionsWrapper').removeClass("visible");
        }
        else {
            $('.batchActionsWrapper').addClass("visible");
        }
    };

    public static triggerAction(strTitle: string, strUrl: string, bitRenderInfo: boolean) {
        this.arrSystemids = [];
        this.strCurrentUrl = strUrl;
        this.strCurrentTitle = strTitle;
        this.bitRenderInfo = bitRenderInfo;

        //get the selected elements
        this.arrSystemids = this.getSelectedElements();

        if(this.arrSystemids.length == 0)
            return;

        var curConfirm = this.strConfirm.replace('%amount%', ""+this.arrSystemids.length);
        curConfirm = curConfirm.replace('%title%', strTitle);

        jsDialog_1.setTitle(this.strDialogTitle);
        jsDialog_1.setContent(curConfirm, this.strDialogStart, 'javascript:require(\'lists\').executeActions();', true);
        jsDialog_1.init();

        //reset pending list on hide
        var me = this;
        $('#'+jsDialog_1.containerId).on('hidden.bs.modal', function () {
            me.arrSystemids = [];
        });

        // reset messages
        if (this.bitRenderInfo) {
            $('.batchaction_messages_list').html("");
        }

        return false;
    };

    public static executeActions() {
        this.intTotal = this.arrSystemids.length;

        $('.batchActionsProgress > .progresstitle').text(this.strCurrentTitle);
        $('.batchActionsProgress > .total').text(this.intTotal);
        jsDialog_1.setContentRaw($('.batchActionsProgress').html());

        this.triggerSingleAction();
    };

    public static triggerSingleAction() {
        if(this.arrSystemids.length > 0 && this.intTotal > 0) {
            $('.batch_progressed').text((this.intTotal - this.arrSystemids.length +1));
            var intPercentage = ( (this.intTotal - this.arrSystemids.length) / this.intTotal * 100);
            $('.progress > .progress-bar').css('width', intPercentage+'%');
            $('.progress > .progress-bar').html(Math.round(intPercentage)+'%');

            var strUrl = this.strCurrentUrl.replace("%systemid%", this.arrSystemids[0]);
            this.arrSystemids.shift();

            var me = this;
            $.ajax({
                type: 'POST',
                url: strUrl,
                success: function(resp) {
                    me.triggerSingleAction();
                    if (me.bitRenderInfo) {
                        var data = JSON.parse(resp);
                        if (data && data.message) {
                            $('.batchaction_messages_list').append("<li>" + data.message + "</li>");
                        }
                    }
                },
                dataType: 'text'
            });
        }
        else {
            $('.batch_progressed').text((this.intTotal));
            $('.progress > .progress-bar').css('width', 100+'%');
            $('.progress > .progress-bar').html('100%');

            if (!this.bitRenderInfo) {
                require('router').reload();
                jsDialog_1.hide();
            }
            else {
                $('#jsDialog_1_cancelButton').css('display', 'none');
                $('#jsDialog_1_confirmButton').remove('click').on('click', function() {document.location.reload();}).html('<span data-lang-property="system:systemtask_close_dialog"></span>');
                lang.initializeProperties($('#jsDialog_1_confirmButton'));
            }
        }
    };

    /**
     * Creates an array which contains the selected system id's.
     *
     * @returns {Array}
     */
    public static getSelectedElements() {
        var selectedElements : Array<string> = [];

        //get the selected elements
        $("table.admintable  input:checked").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                var sysid = $(this).closest("tr").data('systemid');
                if(sysid != "")
                    selectedElements.push(sysid);
            }
        });

        return selectedElements;
    };

    /**
     * Creates an array which contains all system id's.
     *
     * @returns {Array}
     */
    public static getAllElements() {
        var selectedElements : Array<string> = [];

        //get the selected elements
        $("table.admintable  input[type='checkbox']").each(function() {
            if($(this).attr('id').substr(0, 6) == "kj_cb_" && $(this).attr('id') != 'kj_cb_batchActionSwitch') {
                var sysid = $(this).closest("tr").data('systemid');
                if(sysid != "")
                    selectedElements.push(sysid);
            }
        });

        return selectedElements;
    };

    /**
     * Enables selection by clicking a row-entry
     */
    public static initRowClick() {
        var dialog = utils.isStackedDialog();
        var tds = $('#moduleOutput .admintable tr td');
        tds.addClass('clickable');
        tds.on('click', function(e) {

            var source = e.target;
            //if not fired within an td, skip
            if (source.tagName.toLowerCase() != "td") {
                return;
            }

            var row = $(this).parent('tr');

            var callbacks = row.find('td.actions .listButton a[onclick*="selectCallback"]');
            if (callbacks.length) {
                callbacks[0].click();
                return;
            }
            if (dialog) {
                var button = row.find('td.actions .listButton:last a');
            } else {
                var button = row.find('td.actions .listButton:first a');
            }
            if (button.length) {
                var attr = button.attr('data-toggle');
                if (typeof attr !== typeof undefined) {
                    e.stopPropagation();
                    button.dropdown('toggle');
                }

                button[0].click();
            }
        });
    };

}

export = Lists;
