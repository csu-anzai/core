<?php

namespace Kajona\System\Tests\Lifecycle;

use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Lifecycle\ServiceLifeCycleImpl;
use Kajona\System\System\ServiceProvider;
use Kajona\System\Tests\Testbase;
use Pimple\Container;

class ServiceLifeCycleFactoryTest extends Testbase
{
    /**
     * @var ServiceLifeCycleFactory
     */
    protected $objServiceFactory;

    protected function setUp()
    {
        $objContainer = new Container();
        $objContainer[ServiceProvider::STR_LIFE_CYCLE_FACTORY] = function(Container $c){
            return new ServiceLifeCycleFactory($c);
        };
        $objContainer['service_a'] = function(Container $c){
            return new ServiceA($c[ServiceProvider::STR_LIFE_CYCLE_FACTORY]);
        };
        $objContainer[ServiceProvider::STR_LIFE_CYCLE_DEFAULT] = function(Container $c){
            return new ServiceDefault($c[ServiceProvider::STR_LIFE_CYCLE_FACTORY]);
        };

        $this->objServiceFactory = $objContainer[ServiceProvider::STR_LIFE_CYCLE_FACTORY];
    }

    public function testFactory()
    {
        $objService = $this->objServiceFactory->factory(ModelA::class);

        $this->assertInstanceOf(ServiceA::class, $objService);
    }

    public function testFactoryWithoutAnnotation()
    {
        $objService = $this->objServiceFactory->factory(ModelB::class);

        $this->assertInstanceOf(ServiceLifeCycleImpl::class, $objService);
    }

    public function testGetLifeCycle()
    {
        $objService = ServiceLifeCycleFactory::getLifeCycle(ModelB::class);

        $this->assertInstanceOf(ServiceLifeCycleImpl::class, $objService);
    }
}

/**
 * @lifeCycleService service_a
 */
class ModelA
{
}

class ServiceA extends ServiceLifeCycleImpl
{
}

class ModelB
{
}

class ServiceDefault extends ServiceLifeCycleImpl
{
}
