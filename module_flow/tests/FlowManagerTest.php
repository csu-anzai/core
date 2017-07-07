<?php

namespace Kajona\System\Tests;

use Kajona\Flow\System\FlowConfig;
use Kajona\Flow\System\FlowHandlerAbstract;
use Kajona\Flow\System\FlowManager;
use Kajona\Flow\System\FlowModelTrait;
use Kajona\Flow\System\FlowStatus;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\System\Model;
use Kajona\System\System\Root;

class FlowManagerTest extends Testbase
{
    /**
     * @var FlowManager
     */
    protected $objManager;

    /**
     * @var FlowConfig
     */
    protected $objFlow;

    protected function setUp()
    {
        parent::setUp();

        $this->objManager = new FlowManager();
        $this->objFlow = FlowConfig::getByModelClass(FlowModelTest::class);
        if ($this->objFlow instanceof FlowConfig) {
            // we have already a test flow config
            return;
        }

        $this->objFlow = new FlowConfig();
        $this->objFlow->setStrName("dev");
        $this->objFlow->setStrTargetClass(FlowModelTest::class);
        $this->objFlow->setStrHandlerClass(FlowHandlerTest::class);
        $this->objFlow->setIntRecordStatus(1);
        $this->objFlow->updateObjectToDb();

        $objRedStatus = new FlowStatus();
        $objRedStatus->setStrName("In Bearbeitung");
        $objRedStatus->setStrIcon("icon_flag_red");
        $objRedStatus->updateObjectToDb($this->objFlow->getSystemid());

        $objGreenStatus = new FlowStatus();
        $objGreenStatus->setStrName("Freigegeben");
        $objGreenStatus->setStrIcon("icon_flag_green");
        $objGreenStatus->updateObjectToDb($this->objFlow->getSystemid());

        $objTransition = new FlowTransition();
        $objTransition->setStrTargetStatus($objGreenStatus->getSystemid());
        $objTransition->updateObjectToDb($objRedStatus->getSystemid());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->objFlow->deleteObjectFromDatabase();
    }

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
    }

    public function testGetNextTransitionForModel()
    {
        $objModel = new FlowModelTest();
        $objModel->setIntRecordStatus(0);

        $objTransition = $this->objManager->getNextTransitionForModel($objModel);

        $this->assertInstanceOf(FlowTransition::class, $objTransition);
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

class FlowModelTest extends Model
{
}

class FlowHandlerTest extends FlowHandlerAbstract
{
    public function getTitle()
    {
        return __CLASS__;
    }

    public function getTargetClass()
    {
        return FlowModelTest::class;
    }

    public function getAvailableActions()
    {
        return [];
    }

    public function getAvailableConditions()
    {
        return [];
    }
}

