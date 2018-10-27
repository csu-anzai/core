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
     * @param chartGlobalOptions
     */
    chartjsHelper.createChart = function (ctx, chartData, chartGlobalOptions) {
        require(['chartjs', 'chartjsHelper'], function (chartjs, chartjsHelper) {
            require(['chartjs-plugin-datalabels'], function (chartjsDatalabels) {
                ctx.style.backgroundColor = chartGlobalOptions['backgroundColor'];
                Chart.defaults.global.defaultFontFamily = chartGlobalOptions['defaultFontFamily'];
                // Chart.defaults.global.defaultFontSize = 21;
                Chart.defaults.global.elements.line.fill = false;
                Chart.defaults.global.elements.line.lineTension = 0.9;
                Chart.defaults.global.tooltipCaretSize = 0;

                // if (typeof (chartGlobalOptions['setDefaultTooltip']) == 'undefined' || !chartGlobalOptions['setDefaultTooltip']) {
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

                if (typeof (chartGlobalOptions['createImageLink']) !== 'undefined' || chartGlobalOptions['createImageLink']) {
                    chartData['options']['animation'] = {
                        onComplete: createExportLink
                    }
                }

                if (typeof (chartGlobalOptions['notShowNullValues']) !== 'undefined' || chartGlobalOptions['notShowNullValues']) {
                    chartData['options']['plugins']['datalabels'] = {
                        formatter: function (value) {
                            return chartjsHelper.dataNotShowNullValues(value);
                        }
                    }
                }

                if (typeof (chartGlobalOptions['percentageValues']) !== 'undefined' || chartGlobalOptions['percentageValues']) {
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
                    document.getElementById('".$strLinkExportId."').href = url;
                }

            });
        });
    }

    return chartjsHelper;
});