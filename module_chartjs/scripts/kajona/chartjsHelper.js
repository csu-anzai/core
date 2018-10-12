define(['jquery', 'folderview'], function($, folderview) {

    var chartjsHelper = {};

    chartjsHelper.onClickHandler = function(ev, seriesIndex,pointIndex, dataPoint) {
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

    chartjsHelper.dataPointOnClickURLHandler = function (dataPoint) {
        console.log(dataPoint);
        if (dataPoint.actionhandlervalue && dataPoint.actionhandlervalue != null && dataPoint.actionhandlervalue != "") {
            folderview.dialog.setContentIFrame(dataPoint.actionhandlervalue);
            folderview.dialog.setTitle('');
            folderview.dialog.init();
        }
    };

    return chartjsHelper;
});