<?php

namespace Kajona\Flow\Tests;

// @TODO unfortunately we have no autoloading for the tests folder
use Kajona\Flow\System\FlowTransition;

require_once __DIR__ . "/FlowTestAbstract.php";

class FlowConfigTest extends FlowTestAbstract
{
    public function testUpdateObjectToDb()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        // deactivate config
        $objFlow->setIntRecordStatus(0);
        $bitReturn = $objFlow->updateObjectToDb();

        $this->assertTrue($bitReturn);

        // activate config
        $objFlow->setIntRecordStatus(1);
        $bitReturn = $objFlow->updateObjectToDb();

        $this->assertTrue($bitReturn);
    }

    public function testCopyObject()
    {
        $objFlow = $this->objManager->getFlowForClass(FlowModelTest::class);

        $bitReturn = $objFlow->copyObject();

        $this->assertTrue($bitReturn);

        $objFlow->deleteObjectFromDatabase();
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
