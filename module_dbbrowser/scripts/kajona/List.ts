///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="dbbrowser-list"/>

import * as Vue from "vue";
import Ajax = require("../../../module_system/scripts/kajona/Ajax");

Vue.component('dbbrowser-list', {
    data: function () {
        return {
            headline: "",
            tables: []
        }
    },
    created: function(){
        var self = this;
        Ajax.genericAjaxCall("dbbrowser", "apiListTables", null, function(resp: any){
            var data = JSON.parse(resp);

            self.headline = data.headline;
            data.tables.map(function(e: string){
                self.tables.push(e)
            });
        });
    },
    methods: {
        loadDetail: function(tableName: string){
            Ajax.loadUrlToElement('.schemaDetails', '/xml.php?admin=1&module=dbbrowser&action=apiSystemSchema', {table: tableName});
        }
    },
    template: `<div class="dbbrowser-list">
    <h2>{{ headline }}</h2>
    <table class="table admintable table-striped-tbody">
        <tbody v-for="(tableName, index) in tables">
            <tr>
                <td v-on:click="loadDetail(tableName)">
                    {{ tableName }}             
                </td>
            </tr>
        </tbody>
    </table>
</div>`
});
