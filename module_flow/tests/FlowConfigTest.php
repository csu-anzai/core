<?php

namespace Kajona\Flow\Tests;

// @TODO unfortunately we have no autoloading for the tests folder
use Kajona\Flow\System\FlowTransition;
use Kajona\System\System\Lifecycle\ServiceLifeCycleCopyException;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException;

require_once __DIR__ . "/FlowTestAbstract.php";

class FlowConfigTest extends FlowTestAbstract
{
    public function testUpdateObjectToDb()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        // deactivate config
        $objFlow->setIntRecordStatus(0);
        try {
            ServiceLifeCycleFactory::getLifeCycle(get_class($objFlow))->update($objFlow);
            $this->assertTrue(true);
        } catch (ServiceLifeCycleUpdateException $e) {
            $this->fail("Saving flow not possible");
        }

        // activate config
        $objFlow->setIntRecordStatus(1);
        try {
            ServiceLifeCycleFactory::getLifeCycle(get_class($objFlow))->update($objFlow);
            $this->assertTrue(true);
        } catch (ServiceLifeCycleUpdateException $e) {
            $this->fail("Saving flow not possible");
        }
    }

    public function testCopyObject()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        try {
            ServiceLifeCycleFactory::getLifeCycle(get_class($objFlow))->copy($objFlow);
            $this->assertTrue(true);
        } catch (ServiceLifeCycleCopyException $e) {
            $this->fail("copy flow not possible");
        }

        ServiceLifeCycleFactory::getLifeCycle(get_class($objFlow))->deleteObjectFromDatabase($objFlow);
    }

    public function testHasTransition()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        $objModel = new FlowModelTest();
        $objModel->setIntRecordStatus(0);

        $objTransition = $this->objManager->getNextTransitionForModel($objModel);

        $this->assertTrue($objFlow->hasTransition(0, $objTransition));
    }

    public function testHasTransitionInvalid()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        $objTransition = new FlowTransition();
        $objTransition->setStrSystemid(generateSystemid());

        $this->assertFalse($objFlow->hasTransition(0, $objTransition));
    }
}
