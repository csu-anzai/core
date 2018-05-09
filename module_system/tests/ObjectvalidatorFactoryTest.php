<?php

namespace Kajona\System\Tests;

use Kajona\System\Admin\ObjectvalidatorFactory;
use Kajona\System\System\Model;
use Kajona\System\System\SystemModule;
use Kajona\System\System\Validators\ObjectvalidatorBase;

class ObjectvalidatorFactoryTest extends Testbase
{
    public function testFactory()
    {
        $validator = ObjectvalidatorFactory::factory(new DummyObjectvalidatorModel());

        $this->assertInstanceOf(DummyObjectvalidator::class, $validator);
    }

    public function testFactoryNoValidator()
    {
        $validator = ObjectvalidatorFactory::factory(new SystemModule());

        $this->assertNull($validator);
    }

    /**
     * @expectedException \Kajona\System\System\Exception
     */
    public function testFactoryInvalidValue()
    {
        ObjectvalidatorFactory::factory('foo');

        $this->assertTrue(true);
    }
}

/**
 * @objectValidator Kajona\System\Tests\DummyObjectvalidator
 */
class DummyObjectvalidatorModel
{
}

class DummyObjectvalidator extends ObjectvalidatorBase
{
    public function validateObject(Model $objObject)
    {
    }
}

