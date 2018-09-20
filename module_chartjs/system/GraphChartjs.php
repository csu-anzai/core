<?php
/*"******************************************************************************************************
*   (c) 2010-2018 ARTEMEON                                                                              *
********************************************************************************************************/

namespace Kajona\Chartjs\System;

use Kajona\System\System\GraphInterface;
use Kajona\System\System\Resourceloader;

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
     * Defines the lable for the left side (x-axis) of the chart
     *
     * @var string
     */
    private $strXAxisTitle = '';

    /**
     * Defines the lable for the bottom (y-axis) of the chart
     *
     * @var string
     */
    private $strYAxisTitle = '';

    /**
     * Defines the title of the graph itself
     *
     * @var string
     */
    private $strGraphTitle = '';

    /**
     * Defines the type of the chart [line, bar, radar, polar area, doughnut, pie, bubble]
     *
     * @var string
     */
    private $strChartType = 'bar';

    /**
     * Defines the type of the chart [line, bar, radar, polar area, doughnut, pie, bubble]
     *
     * @var integer
     */
    private $intBorderWidth = 1;

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
     * @param bool $bitWriteValues
     *
     * @see GraphInterface::addBarChartSet()
     */
    public function addBarChartSet($arrValues, $strLegend, $bitWriteValues = false)
    {

    }

    /**
     * @param array $arrValues
     * @param string $strLegend
     * @param bool $bitWriteValues
     *
     * @see GraphInterface::addStackedBarChartSet()
     */
    public function addStackedBarChartSet($arrValues, $strLegend, $bitWriteValues = true)
    {

    }

    /**
     * @param array $arrValues
     * @param string $strLegend
     *
     * @see GraphInterface::addLinePlot()
     */
    public function addLinePlot($arrValues, $strLegend)
    {

    }

    /**
     * @param array $arrValues
     * @param array $arrLegends
     *
     * @see GraphInterface::createPieChart()
     */
    public function createPieChart($arrValues, $arrLegends)
    {

    }

    /**
     * @see GraphInterface::showGraph()
     */
    public function showGraph()
    {

    }

    /**
     * @param $strFilename
     *
     * @see GraphInterface::saveGraph()
     */
    public function saveGraph($strFilename)
    {

    }

    /**
     * @return mixed
     *
     * @see GraphInterface::renderGraph()
     */
    public function renderGraph()
    {
        $this->setStrXAxisTitle('xAxis Title');
        $this->setStrYAxisTitle('yAxis Title');
        $this->setStrGraphTitle('ARTEMEON Graph Chart.js');
        $this->setStrChartType('bar');
        $this->setIntBorderWidth(1);
        $this->setBitResponsive(true);

        $arrayColors = [
            0 => ['name' => 'Absolute Zero', 'hex' => '#0048Ba', 'rgba' => '0, 72,186'],
            1 => ['name' => 'Acid', 'hex' => '#B0BF1A', 'rgba' => '176, 191 26'],
            2 => ['name' => 'Alloy Orange', 'hex' => '#C46210', 'rgba' => '196, 98 16'],
            3 => ['name' => 'Amber', 'hex' => '#FFBF00', 'rgba' => '255, 191, 0'],
            4 => ['name' => 'Amethyst', 'hex' => '#9966CC', 'rgba' => '153, 102, 204'],
            5 => ['name' => 'Antique ruby', 'hex' => '#841B2D', 'rgba' => '132, 27, 45'],
            6 => ['name' => 'Antique white', 'hex' => '#FAEBD7', 'rgba' => '250, 235, 215'],
            7 => ['name' => 'Apple green', 'hex' => '#8DB600', 'rgba' => '141, 182, 0'],
            8 => ['name' => 'Arctic lime', 'hex' => '#D0FF14', 'rgba' => '208, 255, 20'],
            9 => ['name' => 'Atomic tangerine', 'hex' => '#FF9966', 'rgba' => '255, 153, 102'],
            10 => ['name' => 'Azure', 'hex' => '#007FFF', 'rgba' => '0, 127, 255'],
            11 => ['name' => 'Baker-Miller pink', 'hex' => '#FF91AF', 'rgba' => '255, 145, 175'],
            12 => ['name' => 'Barbie pink', 'hex' => '#E94196', 'rgba' => '233, 65, 150'],
            13 => ['name' => 'Bitter lemon', 'hex' => '#CAE00D', 'rgba' => '202, 224, 13'],
            14 => ['name' => 'Black coral', 'hex' => '#54626F', 'rgba' => '84, 98, 111']
        ];

        $labels = [];
        $backgroundColors = [];
        $borderColors = [];
        $blockedIndex = [];
        for ($i = 0; $i <= 5; $i++) {
            $rng = rand(0, 14);
            while(in_array($rng, $blockedIndex)){
                $rng = rand(0, 14);
            }

            array_push($blockedIndex, $rng);
            array_push($labels, $arrayColors[$rng]['name']);
            array_push($backgroundColors, 'rgba(' . $arrayColors[$rng]['rgba'] . ', 0.3)');
            array_push($borderColors, $arrayColors[$rng]['hex']);
        }

        $data = [rand(2, 20), rand(5, 15), rand(3, 18), rand(6, 20), rand(4, 17), rand(1, 20)];
        $jsonData = $this->getJsonFormat($data);

        $jsonLabels = $this->getJsonFormat($labels);
        $jsonBackgroundColors = $this->getJsonFormat($backgroundColors);
        $jsonBorderColors = $this->getJsonFormat($borderColors);

        $finalData = [
            'datasetLabel' => 'Dataset 1',
            'labels' => $jsonLabels,
            'data' => $jsonData,
            'backgroundColor' => $jsonBackgroundColors,
            'borderColor' => $jsonBorderColors
        ];

        $strReturn = '<canvas id="chartCanvas" width="' . $this->intWidth . '" height="' . $this->intHeight . '"></canvas>';
        $strReturn .= "<script type='text/javascript'>

            require(['chartjs'], function(chartjs) {

                var ctx = document.getElementById('chartCanvas').getContext('2d');
                var myChart = new chartjs.Chart(ctx, {
                    type: '{$this->strChartType}',
                    data: 
                    {
                        labels: {$finalData["labels"]},
                        datasets: [
                            {
                                label: '{$finalData["datasetLabel"]}',
                                data: {$finalData["data"]},
                                backgroundColor: {$finalData["backgroundColor"]},
                                borderColor: {$finalData["borderColor"]},
                                borderWidth: {$this->intBorderWidth}
                            }
                        ]
                    },                    
                    options: {
                        title: {
                            display: true,
                            text: '{$this->strGraphTitle}'
                        },
                        responsive: {$this->bitResponsive},
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 0,
                                    callback: function(value, index, values) {
                                        return '# ' + value;
                                    }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: '{$this->strYAxisTitle}'
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    fontColor: 'blue',
                                    fontSize: 14,
                                    stepSize: 1,
                                    beginAtZero: true
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: '{$this->strXAxisTitle}'
                                }
                            }]
                        }
                    }
                });     
            });
        </script>";

        return $strReturn;
    }

    /**
     * @param $data
     * @return string
     */
    private function getJsonFormat($data)
    {
        return is_string($data) && is_array(json_decode($data, true)) && (json_last_error() == JSON_ERROR_NONE) ? $data : json_encode($data);
    }

    /**
     * @param string $strTitle
     *
     * @see GraphInterface::setStrXAxisTitle()
     */
    public function setStrXAxisTitle($strTitle)
    {
        $this->strXAxisTitle = $strTitle;
    }

    /**
     * @param string $strTitle
     *
     * @see GraphInterface::setStrYAxisTitle()
     */
    public function setStrYAxisTitle($strTitle)
    {
        $this->strYAxisTitle = $strTitle;
    }

    /**
     * @param string $strTitle
     *
     * @see GraphInterface::setStrGraphTitle()
     */
    public function setStrGraphTitle($strTitle)
    {
        $this->strGraphTitle = $strTitle;
    }

    /**
     * @param string $chartType
     */
    public function setStrChartType(string $chartType)
    {
        $this->strChartType = $chartType;
    }







    /**
     * @param string $strColor
     *
     * @see GraphInterface::setStrBackgroundColor()
     */
    public function setStrBackgroundColor($strColor)
    {

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
     * @param array $arrXAxisTickLabels
     * @param int $intNrOfWrittenLabels
     *
     * @see GraphInterface::setArrXAxisTickLabels()
     */
    public function setArrXAxisTickLabels($arrXAxisTickLabels, $intNrOfWrittenLabels = 12)
    {

    }

    /**
     * @param bool $bitRenderLegend
     *
     * @see GraphInterface::setBitRenderLegend()
     */
    public function setBitRenderLegend($bitRenderLegend)
    {

    }

    /**
     * @param string $strFont
     *
     * @see GraphInterface::setStrFont()
     */
    public function setStrFont($strFont)
    {

    }

    /**
     * @param string $strFontColor
     *
     * @see GraphInterface::setStrFontColor()
     */
    public function setStrFontColor($strFontColor)
    {

    }

    /**
     * @param int $intXAxisAngle
     *
     * @see GraphInterface::setIntXAxisAngle()
     */
    public function setIntXAxisAngle($intXAxisAngle)
    {

    }

    /**
     * @param array $arrSeriesColors
     * @return mixed
     *
     * @see GraphInterface::setArrSeriesColors()
     */
    public function setArrSeriesColors($arrSeriesColors)
    {
        return '';
    }

    /**
     * @param int $intBorderWidth
     */
    public function setIntBorderWidth(int $intBorderWidth)
    {
        $this->intBorderWidth = $intBorderWidth;
    }

    /**
     * @param int $bitResponsive
     */
    public function setBitResponsive(int $bitResponsive)
    {
        $this->bitResponsive = $bitResponsive;
    }

}
