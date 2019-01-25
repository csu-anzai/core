///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="dbbrowser"/>

import Ajax = require("../../../module_system/scripts/kajona/Ajax");

/**
 * Fronend controller for the dbbrowser module
 */
class Dbbrowser {

    /**
     * creates a new index for the column
     * @param tableName
     * @param column
     */
    public static addIndex(tableName: string, column: string) {
        Ajax.genericAjaxCall("dbbrowser", "apiAddIndex", "&table="+tableName+"&column="+column, function(data: any, status: string) {
            if (status == 'success') {
                Ajax.loadUrlToElement('.schemaDetails', '/xml.php?module=dbbrowser&action=apiSystemSchema&table='+tableName);
            }
        }, null, null, 'post', 'json');

    }

    /**
     * Deletes an index from the table
     * @param tableName
     * @param indexName
     */
    public static deleteIndex(tableName: string, indexName: string) {
        Ajax.genericAjaxCall("dbbrowser", "apiDeleteIndex", "&table="+tableName+"&index="+indexName, function(data: any, status: string) {
            if (status == 'success') {
                Ajax.loadUrlToElement('.schemaDetails', '/xml.php?module=dbbrowser&action=apiSystemSchema&table='+tableName);
            }
        }, null, null, 'post', 'json');

    }

    /**
     * Recreates an index
     * @param tableName
     * @param indexName
     */
    public static recreateIndex(tableName: string, indexName: string) {
        Ajax.genericAjaxCall("dbbrowser", "apiRecreateIndex", "&table="+tableName+"&index="+indexName, function(data: any, status: string) {
            if (status == 'success') {
                Ajax.loadUrlToElement('.schemaDetails', '/xml.php?module=dbbrowser&action=apiSystemSchema&table='+tableName);
            }
        }, null, null, 'post', 'json');

    }

}

export = Dbbrowser;