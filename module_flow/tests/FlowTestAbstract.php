<?php

namespace Kajona\Flow\Tests;

use Kajona\Flow\System\FlowConfig;
use Kajona\Flow\System\FlowHandlerAbstract;
use Kajona\Flow\System\FlowManager;
use Kajona\Flow\System\FlowStatus;
use Kajona\Flow\System\FlowTransition;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\Tests\Testbase;

abstract class FlowTestAbstract extends Testbase
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
        $this->objFlow->setBitValidateConsistency(false);
        ServiceLifeCycleFactory::getLifeCycle(get_class($this->objFlow))->update($this->objFlow);

        $objRedStatus = new FlowStatus();
        $objRedStatus->setStrName("In Bearbeitung");
        $objRedStatus->setStrIconColor("#FF0000");
        $objRedStatus->setIntIndex(0);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objRedStatus))->update($objRedStatus, $this->objFlow->getSystemid());

        $objGreenStatus = new FlowStatus();
        $objGreenStatus->setStrName("Freigegeben");
        $objGreenStatus->setStrIconColor("#00893d");
        $objGreenStatus->setIntIndex(1);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objGreenStatus))->update($objGreenStatus, $this->objFlow->getSystemid());

        $objTransition = new FlowTransition();
        $objTransition->setStrTargetStatus($objGreenStatus->getSystemid());
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTransition))->update($objTransition, $objRedStatus->getSystemid());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->objFlow->deleteObjectFromDatabase();
    }
}

class FlowModelTest extends Model implements ModelInterface
{
    public function getStrDisplayName()
    {
        return "";
    }
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

