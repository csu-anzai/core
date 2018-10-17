<?php

namespace Kajona\Chartjs\Tests;

use Kajona\Chartjs\System\GraphChartjs;
use Kajona\System\System\Exception;
use Kajona\System\Tests\Testbase;

class ChartjsTest extends Testbase
{

    public function testChartJSBasicParameters()
    {
        $objGraph = new GraphChartjs();

        $arrChartData = $objGraph->getArrChartData();
        $this->assertTrue(isset($arrChartData['type']));
        $this->assertEquals($arrChartData['type'], "bar");

        $weHaveAnExpetion = false;
        try {
            $objGraph->renderGraph();
        } catch (Exception $e) {
            $this->assertEquals('Chart not initialized yet', $e->getMessage());
            $weHaveAnExpetion = true;
        } finally {
            $this->assertTrue($weHaveAnExpetion);
        }

        $objGraph->setStrBackgroundColor('red');
        $objGraph->setStrFontColor('blue');
        $objGraph->setStrFont('Arial');
        $objGraph->setStrLegendFontColor('green');
        $objGraph->addBarChartSet([10, 20, 30], "");
        $objGraph->setStrXAxisTitle('XXX');
        $objGraph->setStrYAxisTitle('YYY');
        $arrChartData = $objGraph->getArrChartData();
        $this->assertEquals($arrChartData['data']['datasets'][0]['label'], "Dataset 0");
        $this->assertEquals($arrChartData['data']['datasets'][0]['data'][0], 10);
        $this->assertEquals($arrChartData['data']['datasets'][0]['data'][1], 20);
        $this->assertEquals($arrChartData['data']['datasets'][0]['data'][2], 30);

        $this->assertTrue($arrChartData['options']['scales']['xAxes'][0]['scaleLabel']['display']);
        $this->assertEquals($arrChartData['options']['scales']['xAxes'][0]['scaleLabel']['labelString'], 'XXX');
        $this->assertTrue($arrChartData['options']['scales']['yAxes'][0]['scaleLabel']['display']);
        $this->assertEquals($arrChartData['options']['scales']['yAxes'][0]['scaleLabel']['labelString'], 'YYY');

        $strRender = $objGraph->renderGraph();

        $this->assertTrue(!empty(strpos($strRender, 'var chartGlobalOptions = {"backgroundColor":"red","defaultFontColor":"blue","defaultFontFamily":"Arial","labelsFontColor":"green"};')));
    }

    public function testChartJSBarChart()
    {
        $objGraph = new GraphChartjs();

        $objGraph->addBarChartSet(array(1, 4, 3, 6), "serie 1");
        $objGraph->addBarChartSet(array(3, 3, 6, 2), "serie 2");
        $objGraph->addBarChartSet(array(4, 4, 8, 6), "serie 3");
        $arrChartData = $objGraph->getArrChartData();
        $this->assertEquals($arrChartData['type'], "bar");
        $this->assertEquals($arrChartData['data']['datasets'][0]['label'], "serie 1");
        $this->assertEquals($arrChartData['data']['datasets'][1]['label'], "serie 2");
        $this->assertEquals($arrChartData['data']['datasets'][2]['label'], "serie 3");

        $objGraph->setBarHorizontal(true);
        $arrChartData = $objGraph->getArrChartData();
        $this->assertEquals($arrChartData['type'], "horizontalBar");
    }

    public function testChartJSLineChart()
    {
        $objGraph = new GraphChartjs();

        $objGraph->addLinePlot(array(1, 2, 7, 0, 10), "serie 1");
        $objGraph->addLinePlot(array(1, 2, 7, 0, 11), "serie 2");

        $arrChartData = $objGraph->getArrChartData();
        $this->assertEquals($arrChartData['type'], "bar");
        $this->assertEquals($arrChartData['data']['datasets'][0]['type'], "line");
        $this->assertEquals($arrChartData['data']['datasets'][0]['label'], "serie 1");
        $this->assertEquals($arrChartData['data']['datasets'][1]['label'], "serie 2");
    }

    public function testChartJSPieChart()
    {
        $objGraph = new GraphChartjs();

        $objGraph->setStrGraphTitle("Pie Chart");
        $objGraph->createPieChart(array(231, 20, 30, 40), array("val 1", "val 2", "val 3", "val 4"));
        $objGraph->createPieChart(array(230, 21, 31, 42), array("val 1", "val 2", "val 3", "another val"));
        $arrChartData = $objGraph->getArrChartData();
        $this->assertTrue($arrChartData['type'] == "pie");
        $this->assertEquals(count($arrChartData['data']['datasets']), 2);
        $this->assertEquals($arrChartData['data']['labels'][0], "val 1");
        $this->assertEquals($arrChartData['data']['labels'][1], "val 2");
        $this->assertEquals($arrChartData['data']['labels'][2], "val 3");
        $this->assertEquals($arrChartData['data']['labels'][3], "another val");
    }
}

