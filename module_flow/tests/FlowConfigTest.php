<?php

namespace Kajona\Flow\Tests;

// @TODO unfortunately we have no autoloading for the tests folder
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
}
