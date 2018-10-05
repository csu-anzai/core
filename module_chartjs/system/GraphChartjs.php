<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Chartjs\System;

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
                'labels' => [
                    //'render' => 'value'
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
    private $bitResponsive = true;

    /**
     * @param array $arrValues
     * @param string $strLegend
     */
    private function addChartSet($arrValues, $strLegend, $type = null) {
        $intDatasetNumber = count($this->arrChartData['data']['datasets']);
        $this->arrChartData['data']['datasets'][] = [
            "type" => $type,
            "label" => !empty($strLegend) ? $strLegend : "Dataset " . $intDatasetNumber,
            "data" => $arrValues,
            "backgroundColor" => 'rgba('.implode(', ', hex2rgb($this->arrColors[$intDatasetNumber])).', 0.3)',
            "borderColor" => $this->arrColors[$intDatasetNumber],
        ];
        $this->intXLabelsCount = count($arrValues);
    }

    /**
     * @param array $arrValues
     * @param string $strLegend
     * @param bool $bitWriteValues
     *
     * @see GraphInterface::addBarChartSet()
     */
    public function addBarChartSet($arrValues, $strLegend, $bitWriteValues = null)
    {
        $this->addChartSet($arrValues, $strLegend);
        if (isset($bitWriteValues)) {
            $this->setWriteValues($bitWriteValues);
        }
    }

    /**
     * @param array $arrValues
     * @param string $strLegend
     * @param bool $bitWriteValues
     *
     * @see GraphInterface::addStackedBarChartSet()
     */
    public function addStackedBarChartSet($arrValues, $strLegend, $bitWriteValues = null)
    {
        $this->addChartSet($arrValues, $strLegend);
        $this->arrChartData['options']['scales']['xAxes'][0]['stacked'] = true;
        $this->arrChartData['options']['scales']['yAxes'][0]['stacked'] = true;
        if (isset($bitWriteValues)) {
            $this->setWriteValues($bitWriteValues);
        }
    }

    /**
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
     * @param array $arrValues
     * @param array $arrLegends
     *
     * @see GraphInterface::createPieChart()
     */
    public function createPieChart($arrValues, $arrLegends)
    {
        $this->setPieChart(true);
        foreach ($this->arrColors as $arrColor) {
            $arrBackgroundColors[] = 'rgba('.implode(', ', hex2rgb($arrColor)).', 0.3)';
            $arrBorderColors[] = $arrColor;
        }
        $this->arrChartData['data']['datasets'][] = [
            "data" => $arrValues,
            "backgroundColor" => $arrBackgroundColors,
            "borderColor" => $arrBorderColors
        ];
        $this->intXLabelsCount = count($arrValues);
        $this->arrChartData['data']['labels'] = $arrLegends;
        $this->setWriteValues(true, 'percentage');
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
     * @return mixed|string
     *
     * @see GraphInterface::renderGraph()
     */
    public function renderGraph()
    {
        if (!isset($this->arrChartData['data']) || count($this->arrChartData['data']) == 0) {
            throw new Exception("Chart not initialized yet", Exception::$level_ERROR);
        }

        if (!isset($this->arrChartData['data']['labels']) || count($this->arrChartData['data']['labels']) == 0) {
            $this->arrChartData['data']['labels'] = range(1, $this->intXLabelsCount );
        }

        $strSystemId = generateSystemid();
        $strResizeableId = "resize_".$strSystemId;
        $strChartId = "chart_".$strSystemId;

        $strWidth = $this->bitIsResponsive ? "100%" : $this->intWidth."px";
        $strReturn = "<div id=\"$strResizeableId\" style=\"width:{$strWidth}; height:".$this->intHeight."px;\">";
        $strReturn .= '<canvas id="'.$strChartId.'" width="'.$this->intWidth.'" height="'.$this->intHeight.'"></canvas>';
        $strReturn .= '</div>';
        $strReturn .= "<script type='text/javascript'>

            require(['chartjs'], function(chartjs) {
                require(['chartjs-plugin-labels'], function(chartjsLabels) {
		            var chartData = ".json_encode($this->arrChartData, JSON_NUMERIC_CHECK).";
    		        var chartGlobalOptions = ".json_encode($this->arrChartGlobalOptions, JSON_NUMERIC_CHECK).";
                    var ctx = document.getElementById('".$strChartId."');
                
                    ctx.style.backgroundColor = chartGlobalOptions['backgroundColor'];
                    Chart.defaults.global.defaultFontColor = typeof (chartGlobalOptions['defaultFontColor']) !== 'undefined' ? chartGlobalOptions['defaultFontColor'] : 'black';
                    Chart.defaults.global.defaultFontFamily = chartGlobalOptions['defaultFontFamily'];
                    Chart.defaults.global.legend.labels.fontColor = typeof (chartGlobalOptions['labelsFontColor']) !== 'undefined' ? chartGlobalOptions['labelsFontColor'] : chartGlobalOptions['defaultFontColor'];    
                
                    new chartjs.Chart(ctx, {
                        type: chartData['type'],
                        data : chartData['data'],
                        options : chartData['options'],
                    });     
                });
            });
        </script>";

        return $strReturn;
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
     * bzw: For progressive  label view use chartjs-plugin-labels plugin
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
     * @param int $bitResponsive
     */
    public function setBitResponsive(int $bitResponsive)
    {
        $this->bitResponsive = $bitResponsive;
    }

    /**
     * @param int $bitHorizontal
     */
    public function setBarHorizontal(int $bitHorizontal)
    {
        $this->arrChartData['type'] = $bitHorizontal ? "horizontalBar" : "bar";
    }

    /**
     * @param int $bitHorizontal
     */
    public function setPieChart(int $bitPie)
    {
        if ($bitPie) {
            $this->arrChartData['type'] = "pie";
        }
    }

    /**
     * @param int $bitHideXAxis
     */
    public function setHideXAxis( int $bitHideXAxis) {
        $this->arrChartGlobalOptions['xAxesTickDispaly'] = $bitHideXAxis ? false : true;
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['display'] = $bitHideXAxis ? false : true;
    }

    /**
     * @param int $bitHideYAxis
     */
    public function setHideYAxis( int $bitHideYAxis) {
        $this->arrChartGlobalOptions['yAxesTickDispaly'] = $bitHideYAxis ? false : true;
        $this->arrChartData['options']['scales']['yAxes'][0]['ticks']['display'] = $bitHideYAxis ? false : true;
    }

    /**
     * @param int $maxXAxesTicksLimit
     */
    public function setMaxXAxesTicksLimit( int $maxXAxesTicksLimit) {
        $this->arrChartGlobalOptions['maxXAxesTicksLimit'] = $maxXAxesTicksLimit;
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['autoSkip'] = true;
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['maxTicksLimit'] = $maxXAxesTicksLimit;
    }

    /**
     * @param bool $beginAtZero
     */
    public function setBeginAtZero($beginAtZero = true){
        $this->arrChartData['options']['scales']['xAxes'][0]['ticks']['beginAtZero'] = $beginAtZero;
    }

    /**
     * Set if you need a value in the chart
     * $type possible values 'label', 'value', 'percentage', 'image' or custom function, default is 'percentage'
     *
     * @param bool $writeValues
     * @param string $type
     */
    public function setWriteValues($writeValues = true, $type = 'value'){
        if ($writeValues) {
            $this->arrChartData['options']['plugins']['labels']['render'] = $type;
        }
        else {
            $this->arrChartData['options']['plugins']['labels'] = [];
        }
    }
}
