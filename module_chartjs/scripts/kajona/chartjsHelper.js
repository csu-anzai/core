/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

/**
 * Chartjs service with helper procedures.
 *
 * @module chartjsHelper
 */
define("chartjsHelper", ['jquery', 'folderview'], function ($, folderview) {

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
            var objFunction = eval("(" + dataPoint.actionhandler + ")");
            if ($.isFunction(objFunction)) {
                objFunction.call(this, ev, seriesIndex, pointIndex, null, dataPoint);
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
        dataArr.map(function (data) {
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

    /**
     * Creates the chart using "chartjs" library and set parameters
     *
     * @param ctx
     * @param chartData
     * @param chartOptions
     */
    chartjsHelper.createChart = function (ctx, chartData, chartOptions) {
        require(['chartjs'], function (chartjs) {

            Chart.defaults.global.defaultFontFamily = '"Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif';
            Chart.defaults.global.defaultFontSize = 10;
            Chart.defaults.global.elements.line.fill = false;
            Chart.defaults.global.elements.line.lineTension = 0.9;
            Chart.defaults.global.tooltips.cornerRadius = 0;
            Chart.defaults.global.tooltips.backgroundColor = 'rgba(255,255,255,0.9)';
            Chart.defaults.global.tooltips.borderWidth = 0.5;
            Chart.defaults.global.tooltips.borderColor = 'rgba(0,0,0,0.8)';
            Chart.defaults.global.tooltips.bodyFontColor = '#000';
            Chart.defaults.global.tooltips.titleFontColor = '#000';
            Chart.defaults.global.legend.labels.boxWidth = 12;
            Chart.defaults.global.maintainAspectRatio = false;


            require(['chartjs-plugin-datalabels'], function (chartjsDatalabels) {
                // set chart area backgroundColor
                ctx.style.backgroundColor = chartOptions['backgroundColor'];

                // if (typeof (chartOptions['setDefaultTooltip']) == 'undefined' || !chartOptions['setDefaultTooltip']) {
                //     chartData['options']['tooltips'] = {
                //         enabled: true,
                //         mode: 'single',
                //
                //         callbacks: {
                //             label: function (tooltipItems, data) {
                //                 return data.datasets[tooltipItems.datasetIndex].label + ' : ' + data.datasets[tooltipItems.datasetIndex].data[tooltipItems.index];
                //             }
                //         },
                //     };
                // }

                if (typeof (chartOptions['createImageLink']) !== 'undefined' || chartOptions['createImageLink']) {
                    chartData['options']['animation'] = {
                        onComplete: createExportLink
                    }
                }

                if (typeof (chartOptions['notShowNullValues']) !== 'undefined' || chartOptions['notShowNullValues']) {
                    chartData['options']['plugins']['datalabels'] = {
                        formatter: function (value) {
                            return chartjsHelper.dataNotShowNullValues(value);
                        }
                    }
                }

                if (typeof (chartOptions['percentageValues']) !== 'undefined' || chartOptions['percentageValues']) {
                    chartData['options']['plugins']['datalabels'] = {
                        formatter: function (value, ctx) {
                            return chartjsHelper.dataShowPercentage(value, ctx);
                        }
                    }
                }

                chartData['options']['onClick'] = function (evt) {
                    var item = this.getElementAtEvent(evt)[0];
                    if (typeof item !== 'undefined') {
                        var datasetIndex = item._datasetIndex;
                        var index = item._index;
                        chartjsHelper.onClickHandler(evt, index, datasetIndex, chartData['data']['datasets'][datasetIndex]['dataPoints'][index]);
                    }
                };

                var myChart = new chartjs.Chart(ctx, {
                    type: chartData['type'],
                    data: chartData['data'],
                    options: chartData['options'],
                });

                function createExportLink() {
                    var url = myChart.toBase64Image();
                    document.getElementById(chartOptions['strLinkExportId']).href = url;
                }

            });
        });
    };

    return chartjsHelper;
});