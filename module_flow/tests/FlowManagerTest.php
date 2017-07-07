<?php

namespace Kajona\Flow\Tests;

use Kajona\Flow\System\FlowConfig;
use Kajona\Flow\System\FlowStatus;
use Kajona\Flow\System\FlowTransition;

// @TODO unfortunately we have no autoloading for the tests folder
require_once __DIR__ . "/FlowTestAbstract.php";

class FlowManagerTest extends FlowTestAbstract
{
    public function getPossibleStatusForModel()
    {
        $arrStatus = $this->objManager->getPossibleStatusForModel(new FlowModelTest());

        $this->assertEquals([
            0 => 'In Bearbeitung',
            1 => 'Freigegeben',
        ], $arrStatus);
    }

    public function testGetPossibleStatusForClass()
    {
        $arrStatus = $this->objManager->getPossibleStatusForClass(FlowModelTest::class);

        $this->assertEquals([
            0 => 'In Bearbeitung',
            1 => 'Freigegeben',
        ], $arrStatus);
    }

    public function testGetPossibleTransitionsForModel()
    {
        $objModel = new FlowModelTest();
        $objModel->setIntRecordStatus(0);

        $arrTransitions = $this->objManager->getPossibleTransitionsForModel($objModel);

        $this->assertEquals(1, count($arrTransitions));

        /** @var FlowTransition $objTransition */
        $objTransition = $arrTransitions[0];

        $this->assertInstanceOf(FlowTransition::class, $objTransition);
        $this->assertEquals(1, $objTransition->getTargetStatus()->getIntIndex());
    }

    public function testGetNextTransitionForModel()
    {
        $objModel = new FlowModelTest();
        $objModel->setIntRecordStatus(0);

        $objTransition = $this->objManager->getNextTransitionForModel($objModel);

        $this->assertInstanceOf(FlowTransition::class, $objTransition);
        $this->assertEquals(1, $objTransition->getTargetStatus()->getIntIndex());
    }

    public function testGetCurrentStepForModel()
    {
        $objModel = new FlowModelTest();
        $objModel->setIntRecordStatus(0);

        $objStep = $this->objManager->getCurrentStepForModel($objModel);

        $this->assertInstanceOf(FlowStatus::class, $objStep);
    }

    public function testGetFlowForModel()
    {
        $objFlow = $this->objManager->getFlowForModel(new FlowModelTest());

        $this->assertInstanceOf(FlowConfig::class, $objFlow);
    }

    public function testGetFlowForClass()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        $this->assertInstanceOf(FlowConfig::class, $objFlow);
    }
}
