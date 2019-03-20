import * as $ from "jquery";
// import * as chartjs from "../chartjs/Chart.bundle.min";
// import "../chartjs/chartjs-plugin-datalabels";
// import Chart from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels.js";
import Folderview from "../../../module_system/scripts/kajona/Folderview";
var Chart = require("chart.js");

declare var Chart: any;

/**
 * Chartjs service with helper procedures.
 */
class ChartjsHelper {
  /**
   * Chartjs onClick handler
   *
   * @param ev
   * @param seriesIndex
   * @param pointIndex
   * @param dataPoint
   */
  public static onClickHandler(
    ev: any,
    seriesIndex: any,
    pointIndex: number,
    dataPoint: any
  ) {
    if (dataPoint.actionhandler && dataPoint.actionhandler != null) {
      var objFunction = eval("(" + dataPoint.actionhandler + ")");
      if ($.isFunction(objFunction)) {
        objFunction.call(this, ev, seriesIndex, pointIndex, null, dataPoint);
      }
    } else {
      this.dataPointOnClickURLHandler(dataPoint);
    }
  }

  /**
   * Opens URL in dialog pop-up window
   *
   * @param dataPoint
   */
  public static dataPointOnClickURLHandler(dataPoint: any) {
    if (
      dataPoint.actionhandlervalue &&
      dataPoint.actionhandlervalue != null &&
      dataPoint.actionhandlervalue != ""
    ) {
      Folderview.dialog.setContentIFrame(dataPoint.actionhandlervalue);
      Folderview.dialog.setTitle("");
      Folderview.dialog.init();
    }
  }

  /**
   * Converts data value in a percentage view
   *
   * @param value
   * @param ctx
   * @returns {string}
   */
  public static dataShowPercentage(value: number, ctx: any) {
    var sum: number = 0;
    var dataArr = ctx.chart.data.datasets[0].data;
    dataArr.map(function(data: number) {
      sum += data;
    });
    var percentage = ((value * 100) / sum).toFixed(2);
    return percentage != "0" ? percentage + "%" : "";
  }

  /**
   * Changes "0" values to empty string
   *
   * @param value
   * @returns {string}
   */
  public static dataNotShowNullValues(value: number) {
    return value != 0 ? value : "";
  }

  /**
   * Creates the chart using "chartjs" library and set parameters
   *
   * @param ctx
   * @param chartData
   * @param chartOptions
   */
  public static createChart(ctx: any, chartData: any, chartOptions: any) {
    Chart.defaults.global.defaultFontFamily =
      '"Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif';
    Chart.defaults.global.defaultFontSize = 10;
    Chart.defaults.global.elements.line.fill = false;
    Chart.defaults.global.elements.line.lineTension = 0.9;
    Chart.defaults.global.tooltips.cornerRadius = 0;
    Chart.defaults.global.tooltips.backgroundColor = "rgba(255,255,255,0.9)";
    Chart.defaults.global.tooltips.borderWidth = 0.5;
    Chart.defaults.global.tooltips.borderColor = "rgba(0,0,0,0.8)";
    Chart.defaults.global.tooltips.bodyFontColor = "#000";
    Chart.defaults.global.tooltips.titleFontColor = "#000";
    Chart.defaults.global.legend.labels.boxWidth = 12;
    Chart.defaults.global.maintainAspectRatio = false;

    // set chart area backgroundColor
    ctx.style.backgroundColor = chartOptions["backgroundColor"];

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

    if (
      typeof chartOptions["createImageLink"] !== "undefined" ||
      chartOptions["createImageLink"]
    ) {
      chartData["options"]["animation"] = {
        onComplete: createExportLink
      };
    }

    if (
      typeof chartOptions["notShowNullValues"] !== "undefined" ||
      chartOptions["notShowNullValues"]
    ) {
      chartData["options"]["plugins"]["datalabels"] = {
        formatter: function(value: number) {
          return ChartjsHelper.dataNotShowNullValues(value);
        }
      };
    }

    if (
      typeof chartOptions["percentageValues"] !== "undefined" ||
      chartOptions["percentageValues"]
    ) {
      chartData["options"]["plugins"]["datalabels"] = {
        formatter: function(value: number, ctx: any) {
          return ChartjsHelper.dataShowPercentage(value, ctx);
        }
      };
    }

    chartData["options"]["onClick"] = function(evt: any) {
      var item = this.getElementAtEvent(evt)[0];
      if (typeof item !== "undefined") {
        var datasetIndex = item._datasetIndex;
        var index = item._index;
        ChartjsHelper.onClickHandler(
          evt,
          index,
          datasetIndex,
          chartData["data"]["datasets"][datasetIndex]["dataPoints"][index]
        );
      }
    };

    var myChart = new Chart.Chart(ctx, {
      type: chartData["type"],
      data: chartData["data"],
      options: chartData["options"]
    });

    function createExportLink() {
      var url = myChart.toBase64Image();
      $("#" + chartOptions["strLinkExportId"]).attr("href", url);
    }
  }
}
(<any>window).ChartjsHelper = ChartjsHelper;
export default ChartjsHelper;
