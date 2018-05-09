<?php

declare(strict_types = 1);

namespace Kajona\System\Tests;

use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Permissions\AddPermissionToGroup;
use Kajona\System\System\Permissions\PermissionActionProcessor;
use Kajona\System\System\Permissions\RemovePermissionFromGroup;
use Kajona\System\System\Rights;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\UserGroup;

class PermissionActionProcessorTest extends Testbase
{
    /**
     * @var SystemAspect
     */
    private static $aspectA;

    /**
     * @var SystemAspect
     */
    private static $aspectB;

    /**
     * @var UserGroup
     */
    private static $groupA;

    /**
     * @var UserGroup
     */
    private static $groupB;

    /**
     * @throws \Kajona\System\System\Exception
     */
    public function setUp()
    {
        parent::setUp();

        self::$aspectA = new SystemAspect();
        ServiceLifeCycleFactory::getLifeCycle(get_class(self::$aspectA))->update(self::$aspectA);
        self::$aspectB = new SystemAspect();
        ServiceLifeCycleFactory::getLifeCycle(get_class(self::$aspectB))->update(self::$aspectB);

        self::$groupA = new UserGroup();
        ServiceLifeCycleFactory::getLifeCycle(get_class(self::$groupA))->update(self::$groupA);

        self::$groupB = new UserGroup();
        ServiceLifeCycleFactory::getLifeCycle(get_class(self::$groupB))->update(self::$groupB);

    }

    public function tearDown()
    {
        parent::tearDown();

        foreach ([self::$aspectA, self::$aspectB, self::$groupA, self::$groupB] as &$obj) {
            if ($obj !== null) {
                $obj->deleteObjectFromDatabase();
                $obj = null;
            }
        }

    }

    /**
     * @throws \Kajona\System\System\Exception
     */
    public function testEmptyActions()
    {
        $permManager = new PermissionActionProcessor(Rights::getInstance());
        $this->assertFalse($permManager->applyActions());
    }

    /**
     * @throws \Kajona\System\System\Exception
     */
    public function testResolvingActions()
    {
        $permManager = new PermissionActionProcessor(Rights::getInstance());
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $permManager->addAction(new RemovePermissionFromGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $this->assertFalse($permManager->applyActions());
    }

    /**
     * @throws \Kajona\System\System\Exception
     */
    public function testSingleAction()
    {
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));

        $permManager = new PermissionActionProcessor(Rights::getInstance());
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $this->assertTrue($permManager->applyActions());

        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));

        $permManager = new PermissionActionProcessor(Rights::getInstance());
        $permManager->addAction(new RemovePermissionFromGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $this->assertTrue($permManager->applyActions());

        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));
    }

    /**
     * @throws \Kajona\System\System\Exception
     */
    public function testMultipleActions()
    {
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_DELETE, self::$aspectA->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectB->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_DELETE, self::$aspectB->getSystemid()));

        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_DELETE, self::$aspectA->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectB->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_DELETE, self::$aspectB->getSystemid()));

        $permManager = new PermissionActionProcessor(Rights::getInstance());
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_DELETE));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_CHANGELOG));
        $permManager->addAction(new RemovePermissionFromGroup(self::$aspectA->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_CHANGELOG));

        $permManager->addAction(new AddPermissionToGroup(self::$aspectB->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectB->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_DELETE));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectB->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_DELETE));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectB->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $permManager->addAction(new AddPermissionToGroup(self::$aspectB->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_DELETE));
        $this->assertTrue($permManager->applyActions());

        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));
        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_DELETE, self::$aspectA->getSystemid()));
        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_CHANGELOG, self::$aspectA->getSystemid()));

        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectB->getSystemid()));
        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_DELETE, self::$aspectB->getSystemid()));
        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectB->getSystemid()));


        $permManager = new PermissionActionProcessor(Rights::getInstance());
        $permManager->addAction(new AddPermissionToGroup(self::$aspectA->getSystemid(), self::$groupB->getSystemid(), Rights::$STR_RIGHT_CHANGELOG));
        $permManager->addAction(new RemovePermissionFromGroup(self::$aspectA->getSystemid(), self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT));
        $this->assertTrue($permManager->applyActions());

        $this->assertTrue(Rights::getInstance()->checkPermissionForGroup(self::$groupB->getSystemid(), Rights::$STR_RIGHT_CHANGELOG, self::$aspectA->getSystemid()));
        $this->assertFalse(Rights::getInstance()->checkPermissionForGroup(self::$groupA->getSystemid(), Rights::$STR_RIGHT_EDIT, self::$aspectA->getSystemid()));

    }

}

