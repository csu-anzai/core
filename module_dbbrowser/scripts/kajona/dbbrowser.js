/**
 * (c) ARTEMEON Management Partner GmbH
 * Published under the GNU LGPL v2.1
 */

/**
 * @module dbbrowser
 */
define('dbbrowser', ['ajax'], function (ajax) {


    return /** @alias module:dbbrowser */ {

        /**
         * creates a new index for the column
         * @param tableName
         * @param column
         */
        addIndex: function (tableName, column) {

            ajax.genericAjaxCall("dbbrowser", "apiAddIndex", "&table="+tableName+"&column="+column, function(data, status) {
                if (status == 'success') {
                    ajax.loadUrlToElement('.schemaDetails', '/xml.php?module=dbbrowser&action=apiSystemSchema&table='+tableName);
                }
            }, null, null, 'post', 'json');

        },

        /**
         * Deletes an index from the table
         * @param tableName
         * @param indexName
         */
        deleteIndex: function (tableName, indexName) {

            ajax.genericAjaxCall("dbbrowser", "apiDeleteIndex", "&table="+tableName+"&index="+indexName, function(data, status) {
                if (status == 'success') {
                    ajax.loadUrlToElement('.schemaDetails', '/xml.php?module=dbbrowser&action=apiSystemSchema&table='+tableName);
                }
            }, null, null, 'post', 'json');

        }


    }


});

