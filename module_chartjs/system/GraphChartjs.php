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
        $this->setStrXAxisTitle('xAxis');
        $this->setStrYAxisTitle('yAxis');
        $this->setStrGraphTitle('ARTEMEON Graph Chart.js');
        $this->setStrChartType('bar');

        $strReturn = '<canvas id="chartCanvas" height="600"></canvas>';
        $strReturn .= "<script type='text/javascript'>

            require(['chartjs'], function(chartjs) {

                var ctx = document.getElementById('chartCanvas').getContext('2d');
                var myChart = new chartjs.Chart(ctx, {
                    type: '{$this->strChartType}',
                    data: {
                        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                        datasets: [
                            {
                            label: 'Dataset 1',
                            data: [12, 19, 8, 5, 7, 3],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            hoverBackgroundColor: 'rgba(232,105,90,0.8)',
                            scaleStepWidth: 1,
                            borderWidth: 1
                        },
                        {
                            label: 'Dataset 2',
                            data: [4, 14, 18, 14, 5, 5],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }
                        ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: '{$this->strGraphTitle}'
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
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
                                    beginAtZero:true
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

    }

    /**
     * @param int $intHeight
     *
     * @see GraphInterface::setIntHeight()
     */
    public function setIntHeight($intHeight)
    {

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

}
