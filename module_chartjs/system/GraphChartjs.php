<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Chartjs\System;

use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\Carrier;
use Kajona\System\System\Exception;
use Kajona\System\System\GraphCommons;
use Kajona\System\System\GraphInterface;

/**
 * This class could be used to create graphs based on the chartjs API.
 * chartjs renders charts on the client side.
 *
 * @package module_chartjs
 * @since 7.1
 * @author sascha.broening@artemeon.de
 */
class GraphChartjs implements GraphInterface
{

    /**
     * Contains all data of the chart
     *
     * @var array
     */
    private $arrChartData = [
        "type" => "bar",
        "options" => [
            'plugins' => [
                'datalabels' => [
                    'display' => false,
                ]
            ],
            "title" => [
                "display" => true,
            ],
            'scales' => [
                'xAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => true
                        ]
                    ]
                ],
                'yAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * Contains all global options of the chart
     *
     * @var array
     */
    private $arrChartGlobalOptions = [];

    /**
     * @var int
     */
    private $intXLabelsCount = 0;

    /**
     * Default color set for chats.
     *
     * @var array
     */
    private $arrColors = [
        "#8bbc21", "#2f7ed8", "#f28f43", "#1aadce", "#77a1e5", "#0d233a", "#c42525", "#a6c96a", "#910000",
        '#0048Ba', '#B0BF1A', '#C46210', '#FFBF00', '#9966CC', '#841B2D', '#FAEBD7', '#8DB600', '#D0FF14', '#FF9966', '#007FFF', '#FF91AF', '#E94196', '#CAE00D', '#54626F'
    ];

    /**
     * Defines the width for the canvas but ONLY if respnsive is set to FALSE
     *
     * @var integer
     */
    private $intWidth = 400;

    /**
     * Defines the height for the canvas
     *
     * @var integer
     */
    private $intHeight = 400;

    /**
     * Defines the behaviour for the chart. If responsive is set to true, the width and height specifications will be ignored
     *
     * @var bool
     */
    private $bitResponsive = false;

    /**
     * Defines if we need to show the download link of chart image on the chart
     *
     * @var bool
     */
    private $bitDownloadLink = false;

    /**
     * @return array
     */
    public function getArrChartData(): array
    {
        return $this->arrChartData;
    }

    /**
     * Converts array of dataPoint object to array of array
     *
     * @param $arrDataPointObjects
     * @return array
     */
    private function dataPointObjArrayToArray($arrDataPointObjects)
    {
        $arrDataPoints = [];
        foreach ($arrDataPointObjects as $objDataPoint) {
            $arrDataPoints[] = [
                "floatvalue" => $objDataPoint->getFloatValue(),
                "actionhandlervalue" => $objDataPoint->getObjActionHandlerValue(),
                "actionhandler" => $objDataPoint->getObjActionHandler(),
            ];
        }
        return $arrDataPoints;
    }

    /**
     * Add new data set to the chart
     *
     * @param array $arrValues
     * @param string $strLegend
     * @param string $type
     */
    private function addChartSet($arrValues, $strLegend, $type = null)
    {
        $arrDataPointObjects = GraphCommons::convertArrValuesToDataPointArray($arrValues);

        $intDatasetNumber = isset($this->arrChartData['data']['datasets']) ? count($this->arrChartData['data']['datasets']) : 0;
        $this->arrChartData['data']['datasets'][] = [
            "dataPoints" => $this->dataPointObjArrayToArray($arrDataPointObjects),
            "type" => $type,
            "label" => !empty($strLegend) ? $strLegend : "Dataset ".$intDatasetNumber,
            "data" => GraphCommons::getDataPointFloatValues($arrDataPointObjects),
            "backgroundColor" => 'rgba('.implode(', ', hex2rgb($this->arrColors[$intDatasetNumber])).', 0.3)',
            "borderColor" => $this->arrColors[$intDatasetNumber],
        ];
        $this->intXLabelsCount = count($arrValues);
    }

    /**
     * Add new data set to the Bar chart
     *
     * @param array $arrValues
     * @param string $strLegend
     * @param bool $bitWriteValues
     *
     * @see GraphInterface::addBarChartSet()
     */
    public function addBarChartSet($arrValues, $strLegend, $bitWriteValues = false)
    {
        $this->addChartSet($arrValues, $strLegend);
        if (isset($bitWriteValues)) {
            $this->setWriteValues($bitWriteValues);
        }
    }

    /**
     * Add new data set to the StackedBar chart
     *
     * @param array $arrValues
     * @param string $strLegend
     * @param bool $bitWriteValues
     *
     * @see GraphInterface::addStackedBarChartSet()
     */
    public function addStackedBarChartSet($arrValues, $strLegend, $bitWriteValues = false)
    {
        $this->addChartSet($arrValues, $strLegend);
        $this->arrChartData['options']['scales']['xAxes'][0]['stacked'] = true;
        $this->arrChartData['options']['scales']['yAxes'][0]['stacked'] = true;
        if (isset($bitWriteValues)) {
            $this->setWriteValues($bitWriteValues);
        }
    }

    /**
     * Add new data set to the Line chart
     *
     * @param array $arrValues
     * @param string $strLegend
     *
     * @see GraphInterface::addLinePlot()
     */
    public function addLinePlot($arrValues, $strLegend)
    {
        $this->addChartSet($arrValues, $strLegend, "line");
    }

    /**
     * Add new data set to the Pie chart
     *
     * @param array $arrValues
     * @param array $arrLegends
     *
     * @see GraphInterface::createPieChart()
     */
    public function createPieChart($arrValues, $arrLegends)
    {
        $arrDataPointObjects = GraphCommons::convertArrValuesToDataPointArray($arrValues);
        $this->setPieChart(true);
        foreach ($this->arrColors as $arrColor) {
            $arrBackgroundColors[] = 'rgba('.implode(', ', hex2rgb($arrColor)).', 0.3)';
            $arrBorderColors[] = $arrColor;
        }
        $this->arrChartData['data']['datasets'][] = [
            "dataPoints" => $this->dataPointObjArrayToArray($arrDataPointObjects),
            "data" => GraphCommons::getDataPointFloatValues($arrDataPointObjects),
            "backgroundColor" => $arrBackgroundColors,
            "borderColor" => $arrBorderColors
        ];
        $this->intXLabelsCount = count($arrValues);
        $this->arrChartData['data']['labels'] = $arrLegends;
        $this->setWriteValues(true);
        $this->setValueTypePercentage(true);
    }

    /**
     * @see GraphInterface::showGraph()
     */
    public function showGraph()
    {
        $this->renderGraph();
    }

    /**
     * @param $strFilename
     *
     * @see GraphInterface::saveGraph()
     */
    public function saveGraph($strFilename)
    {
        //not supported
    }

    /**
     * @param string $strTitle
     *
     * @see GraphInterface::setStrXAxisTitle()
     */
    public function setStrXAxisTitle($strTitle)
    {
        $this->arrChartData['options']['scales']['xAxes'][0]['scaleLabel']['display'] = true;
        $this->arrChartData['options']['scales']['xAxes'][0]['scaleLabel']['labelString'] = $strTitle;
    }

    /**
     * @param string $strTitle
     *
     * @see GraphInterface::setStrYAxisTitle()
     */
    public function setStrYAxisTitle($strTitle)
    {
        $this->arrChartData['options']['scales']['yAxes'][0]['scaleLabel']['display'] = true;
        $this->arrChartData['options']['scales']['yAxes'][0]['scaleLabel']['labelString'] = $strTitle;
    }

    /**
     * @param string $strTitle
     *
     * @see GraphInterface::setStrGraphTitle()
     */
    public function setStrGraphTitle($strTitle)
    {
        $this->arrChartData['options']['title']['dispaly'] = true;
        $this->arrChartData['options']['title']['text'] = $strTitle;
    }

    /**
     * @param string $strColor
     *
     * @see GraphInterface::setStrBackgroundColor()
     */
    public function setStrBackgroundColor($strColor)
    {
        $this->arrChartGlobalOptions['backgroundColor'] = $strColor;
    }

    /**
     * @param int $intWidth
     *
     * @see GraphInterface::setIntWidth()
     */
    public function setIntWidth($intWidth)
    {
        $this->intWidth = $intWidth;
    }

    /**
     * @param int $intHeight
     *
     * @see GraphInterface::setIntHeight()
     */
    public function setIntHeight($intHeight)
    {
        $this->intHeight = $intHeight;
    }

    /**
     * Sets array of labels.
     *
     * @param array $arrXAxisTickLabels
     * @param int $intNrOfWrittenLabels
     *
     * @see GraphInterface::setArrXAxisTickLabels()
     */
    public function setArrXAxisTickLabels($arrXAxisTickLabels, $intNrOfWrittenLabels = 12)
    {
        $this->arrChartData['data']['labels'] = $arrXAxisTickLabels;
        $this->setMaxXAxesTicksLimit($intNrOfWrittenLabels);
    }

    /**
     * @param bool $bitRenderLegend
     *
     * @see GraphInterface::setBitRenderLegend()
     */
    public function setBitRenderLegend($bitRenderLegend)
    {
        $this->arrChartData['options']['legend']['display'] = $bitRenderLegend;
    }

    /**
     * @param string $strFont
     *
     * @see GraphInterface::setStrFont()
     */
    public function setStrFont($strFont)
    {
        $this->arrChartGlobalOptions['defaultFontFamily'] = $strFont;
    }

    /**
     * @param string $strFontColor
     *
     * @see GraphInterface::setStrFontColor()
     */
    public function setStrFontColor($strFontColor)
    {
        $this->arrChartGlobalOptions['defaultFontColor'] = $strFontColor;
    }

    /**
     * @param string $strFontColor
     */
    public function setStrLegendFontColor($strFontColor)
    {
        $this->arrChartGlobalOptions['labelsFontColor'] = $strFontColor;
    }

    /**
     * @see GraphInterface::setIntXAxisAngle()
     */
    public function setIntXAxisAngle($intXAxisAngle)
    {
        //not supported
    }

    /**
     * @param array $arrSeriesColors
     * @return void
     *
     * @see GraphInterface::setArrSeriesColors()
     */
    public function setArrSeriesColors($arrSeriesColors)
    {
        if (!empty($arrSeriesColors)) {
            $this->arrColors = $arrSeriesColors;
        }
    }

    /**
     * Enables general repsonsiveness of the chart. This includes that the chart takes up 100% width of the parent container.
     *
     * @param bool $bitResponsive
     */
    public function setBitResponsive(bool $bitResponsive)
    {
        $this->bitResponsive = $bitResponsive;
    }

    /**
     * @return bool
     */
    public function isBitResponsive(): bool
    {
        return $this->bitResponsive;
    }

    /**
     * @param bool $bitHorizontal
     */
    public function setBarHorizontal(bool $bitHorizontal)
    {
        $this->arrChartData['type'] = $bitHorizontal ? "horizontalBar" : "bar";
    }

    /**
     * @param bool $bitHorizontal
     */
    public function setPieChart(bool $bitPie)
    {
        if ($bitPie) {
            $this->arrChartData['type'] = "pie";
        }
    }

    /**
     * @param bool $bitHideXAxis
     */
    public function setHideXAxis(bool $bitHideXAxis = true)
    {
        $this->arrChartGlobalOptions['xAxesTickDispaly'] = !$bitHideXAxis;
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['display'] = !$bitHideXAxis;
    }

    /**
     * @param bool $bitHideYAxis
     */
    public function setHideYAxis(bool $bitHideYAxis = true)
    {
        $this->arrChartGlobalOptions['yAxesTickDispaly'] = !$bitHideYAxis;
        $this->arrChartData['options']['scales']['yAxes'][0]['ticks']['display'] = !$bitHideYAxis;
    }

    /**
     * @param int $bitHideXAxis
     */
    public function setTickStepXAxis(int $intStep)
    {
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['stepSize'] = $intStep;
    }

    /**
     * @param int $bitHideYAxis
     */
    public function setTickStepYAxis(int $intStep)
    {
        $this->arrChartData['options']['scales']['yAxes'][0]['ticks']['stepSize'] = $intStep;
    }

    /**
     * @param bool $bitHideGridLines
     */
    public function setHideGridLinesXAxis(bool $bitHideGridLines = true)
    {
        $this->arrChartData['options']['scales']['xAxes'][0]['gridLines']['display'] = !$bitHideGridLines;
    }

    /**
     * @param bool $bitHideGridLines
     */
    public function setHideGridLinesYAxis(bool $bitHideGridLines = true)
    {
        $this->arrChartData['options']['scales']['yAxes'][0]['gridLines']['display'] = !$bitHideGridLines;
    }

    /**
     * @param int $maxXAxesTicksLimit
     */
    public function setMaxXAxesTicksLimit(int $maxXAxesTicksLimit)
    {
        $this->arrChartGlobalOptions['maxXAxesTicksLimit'] = $maxXAxesTicksLimit;
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['autoSkip'] = true;
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['maxTicksLimit'] = $maxXAxesTicksLimit;
    }

    /**
     * @param bool $beginAtZero
     */
    public function setBeginAtZero(bool $beginAtZero = true)
    {
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['beginAtZero'] = $beginAtZero;
    }

    /**
     * Set if you need to show values straight on the chart
     *
     * @param bool $writeValues
     * @param string $type
     */
    public function setWriteValues(bool $writeValues = false)
    {
        $this->arrChartData['options']['plugins']['datalabels']['display'] = $writeValues;
    }

    /**
     * @return bool
     */
    public function isBitDownloadLink(): bool
    {
        return $this->bitDownloadLink;
    }

    /**
     * @param bool $bitDownloadLink
     */
    public function setBitDownloadLink(bool $bitDownloadLink): void
    {
        $this->bitDownloadLink = $bitDownloadLink;
    }

    /**
     * Set if you need to show values straight on the chart in percentage view
     *
     * @param bool $bitSetPercentageValues
     */
    public function setValueTypePercentage(bool $bitSetPercentageValues = true)
    {
        $this->arrChartGlobalOptions['percentageValues'] = $bitSetPercentageValues;
    }

    /**
     * Set if you don't want to show 0 values on the chart
     *
     * @param bool $bitNotShowNullValues
     */
    public function setNotShowNullValues(bool $bitNotShowNullValues = true)
    {
        $this->arrChartGlobalOptions['notShowNullValues'] = $bitNotShowNullValues;
    }

    /**
     * Switch on default tooltip.
     * By default chartjs render used customized tooltip.
     *
     * @param bool $bitSetDefaultTooltip
     */
    public function setDefaultTooltip(bool $bitSetDefaultTooltip = true)
    {
        $this->arrChartGlobalOptions['setDefaultTooltip'] = $bitSetDefaultTooltip;
    }

    /**
     * @return mixed|string
     *
     * @see GraphInterface::renderGraph()
     * @throws Exception
     */
    public function renderGraph()
    {
        if (!isset($this->arrChartData['data']) || count($this->arrChartData['data']) == 0) {
            throw new Exception("Chart not initialized yet", Exception::$level_ERROR);
        }

        if (!isset($this->arrChartData['data']['labels']) || count($this->arrChartData['data']['labels']) == 0) {
            $this->arrChartData['data']['labels'] = range(1, $this->intXLabelsCount);
        }

        $strSystemId = generateSystemid();
        $strResizeableId = "resize_".$strSystemId;
        $strChartId = "chart_".$strSystemId;
        $strLinkExportId = $strChartId."_exportlink";

        $strWidth = $this->isBitResponsive() ? "100%" : $this->intWidth."px";
        $strReturn = "<div onmouseover='$(\"#$strLinkExportId\").show();' onmouseout='$(\"#$strLinkExportId\").hide();' id=\"$strResizeableId\" style=\"width:{$strWidth}; height:".$this->intHeight."px;\">";
        $strReturn .= '<canvas id="'.$strChartId.'" width="'.$this->intWidth.'" height="'.$this->intHeight.'"></canvas>';
        if ($this->isBitDownloadLink()) {
            $strImage = AdminskinHelper::getAdminImage("icon_downloads", Carrier::getInstance()->getObjLang()->getLang("commons_save_as_image", "system"));
            $strReturn .= "<div class=\"chartjs-link-bar\"><a class=\"chartjs-image-link\" id=\"$strLinkExportId\" download>$strImage</a></div>";
            $this->arrChartGlobalOptions['createImageLink'] = true;
        }
        $strReturn .= "</div>";

        $strReturn .= "<script type='text/javascript'>

            require(['chartjs', 'chartjsHelper'], function(chartjs, chartjsHelper) {
                require(['chartjs-plugin-datalabels'], function(chartjsDatalabels) {
		            var chartData = ".json_encode($this->arrChartData, JSON_NUMERIC_CHECK).";
    		        var chartGlobalOptions = ".json_encode($this->arrChartGlobalOptions, JSON_NUMERIC_CHECK).";
                    var ctx = document.getElementById('".$strChartId."');
                
                    ctx.style.backgroundColor = chartGlobalOptions['backgroundColor'];
                    Chart.defaults.global.defaultFontColor = typeof (chartGlobalOptions['defaultFontColor']) !== 'undefined' ? chartGlobalOptions['defaultFontColor'] : 'black';
                    Chart.defaults.global.defaultFontFamily = chartGlobalOptions['defaultFontFamily'];
                    Chart.defaults.global.legend.labels.fontColor = typeof (chartGlobalOptions['labelsFontColor']) !== 'undefined' ? chartGlobalOptions['labelsFontColor'] : chartGlobalOptions['defaultFontColor'];
                
                    if (typeof (chartGlobalOptions['setDefaultTooltip']) == 'undefined' || !chartGlobalOptions['setDefaultTooltip']) {
                        chartData['options']['tooltips'] = {
                            enabled: true,
                            mode: 'single',
                            callbacks: {
                                label: function(tooltipItems, data) {
                                    return data.datasets[tooltipItems.datasetIndex].label  + ' : ' + data.datasets[tooltipItems.datasetIndex].data[tooltipItems.index];
                                }
                            }
                        };
                    }
                
                    if (typeof (chartGlobalOptions['createImageLink']) !== 'undefined' || chartGlobalOptions['createImageLink']) {
                       chartData['options']['animation'] = {
                            onComplete: createExportLink
                       }
                    }

                    if (typeof (chartGlobalOptions['notShowNullValues']) !== 'undefined' || chartGlobalOptions['notShowNullValues']) {
                       chartData['options']['plugins']['datalabels'] = {
                          formatter: (value, ctx) => {
                             return chartjsHelper.dataNotShowNullValues(value);
                          }
                       }
                    }

                    if (typeof (chartGlobalOptions['percentageValues']) !== 'undefined' || chartGlobalOptions['percentageValues']) {
                       chartData['options']['plugins']['datalabels'] = {
                          formatter: (value, ctx) => {
                             return chartjsHelper.dataShowPercentage(value, ctx);
                          }
                       }
                    }

                    chartData['options']['onClick'] = function (evt){
                        var item = this.getElementAtEvent(evt)[0];
                        if (typeof item !== 'undefined') {
                            var datasetIndex = item._datasetIndex;
                            var index = item._index; 
                            chartjsHelper.onClickHandler(evt, index, datasetIndex, chartData['data']['datasets'][datasetIndex]['dataPoints'][index]);
                        } 
                    };

                    var myChart = new chartjs.Chart(ctx, {
                        type: chartData['type'],
                        data : chartData['data'],
                        options : chartData['options'],
                    });     
                    
                    function createExportLink() {
                        var url = myChart.toBase64Image();
                        document.getElementById('".$strLinkExportId."').href = url;
                    }
                });
            });
        </script>";

        return $strReturn;
    }
}
