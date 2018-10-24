/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

/**
 * Chartjs service with helper procedures.
 *
 * @module chartjsHelper
 */
define("chartjsHelper", ['jquery', 'folderview'], function($, folderview) {

    var chartjsHelper = {};

    /**
     * Chartjs onClick handler
     *
     * @param ev
     * @param seriesIndex
     * @param pointIndex
     * @param dataPoint
     */
    chartjsHelper.onClickHandler = function (ev, seriesIndex, pointIndex, dataPoint) {
        if (dataPoint.actionhandler && dataPoint.actionhandler != null) {
            var objFunction = eval("(" + objDataPoint.actionhandler + ")");
            if ($.isFunction(objFunction)) {
                objFunction.call(this, ev, seriesIndex, pointIndex, objDataPoint);
            }
        }
        else {
            chartjsHelper.dataPointOnClickURLHandler(dataPoint);
        }
    };

    /**
     * Opens URL in dialog pop-up window
     *
     * @param dataPoint
     */
    chartjsHelper.dataPointOnClickURLHandler = function (dataPoint) {
        if (dataPoint.actionhandlervalue && dataPoint.actionhandlervalue != null && dataPoint.actionhandlervalue != "") {
            folderview.dialog.setContentIFrame(dataPoint.actionhandlervalue);
            folderview.dialog.setTitle('');
            folderview.dialog.init();
        }
    };

    /**
     * Converts data value in a percentage view
     *
     * @param value
     * @param ctx
     * @returns {string}
     */
    chartjsHelper.dataShowPercentage = function (value, ctx) {
        var sum = 0;
        var dataArr = ctx.chart.data.datasets[0].data;
        dataArr.map(data => {
            sum += data;
        });
        var percentage = (value * 100 / sum).toFixed(2);
        return percentage != 0 ? percentage + "%" : '';
    };

    /**
     * Changes "0" values to empty string
     *
     * @param value
     * @returns {string}
     */
    chartjsHelper.dataNotShowNullValues = function (value) {
        return value != 0 ? value : '';
    };

    return chartjsHelper;
});