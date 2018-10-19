define("chartjsHelper", ['jquery', 'folderview'], function($, folderview) {

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
        if (dataPoint.actionhandlervalue && dataPoint.actionhandlervalue != null && dataPoint.actionhandlervalue != "") {
            folderview.dialog.setContentIFrame(dataPoint.actionhandlervalue);
            folderview.dialog.setTitle('');
            folderview.dialog.init();
        }
    };

    chartjsHelper.dataShowPercentage = function (value, ctx) {
        var sum = 0;
        var dataArr = ctx.chart.data.datasets[0].data;
        dataArr.map(data => {
            sum += data;
        });
        var percentage = (value * 100 / sum).toFixed(2);
        return percentage != 0 ? percentage + "%" : '';
    };

    chartjsHelper.dataNotShowNullValues = function (value) {
        return value != 0 ? value : '';
    };

    return chartjsHelper;
});