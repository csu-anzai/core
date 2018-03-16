<?php

namespace Kajona\System\Tests\Permissions;

use Kajona\System\System\Carrier;
use Kajona\System\System\Permissions\PermissionHandlerAbstract;
use Kajona\System\System\Permissions\PermissionHandlerFactory;
use Kajona\System\System\Permissions\PermissionHandlerInterface;
use Kajona\System\System\Root;
use Kajona\System\System\ServiceProvider;
use Kajona\System\Tests\Testbase;

/**
 * @author christoph.kappestein@artemeon.de
 */
class PermissionHandlerFactoryTest extends Testbase
{
    public function setUp()
    {
        parent::setUp();

        $objContainer = Carrier::getInstance()->getContainer();
        $objContainer["perm_test_service"] = function($c){
            return new PermService(
                $c[ServiceProvider::STR_OBJECT_FACTORY],
                $c[ServiceProvider::STR_RIGHTS],
                $c[\Kajona\Flow\System\ServiceProvider::STR_MANAGER]
            );
        };
        $objContainer["perm_test_service_invalid"] = function($c){
            return new \stdClass();
        };
    }

    public function tearDown()
    {
        parent::tearDown();

        $objContainer = Carrier::getInstance()->getContainer();
        unset($objContainer["perm_test_service"]);
        unset($objContainer["perm_test_service_invalid"]);
    }

    public function testFactory()
    {
        $objContainer = Carrier::getInstance()->getContainer();
        $objFactory = new PermissionHandlerFactory($objContainer);

        $objPermHandler = $objFactory->factory(PermTestModelWithHandler::class);

        $this->assertInstanceOf(PermissionHandlerInterface::class, $objPermHandler);
    }

    public function testFactoryNoHandler()
    {
        $objContainer = Carrier::getInstance()->getContainer();
        $objFactory = new PermissionHandlerFactory($objContainer);

        $objPermHandler = $objFactory->factory(PermTestModelWithoutHandler::class);

        $this->assertNull($objPermHandler);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Provided permission handler is not an instance of Kajona\System\System\Permissions\PermissionHandlerInterface
     */
    public function testFactoryInvalidHandler()
    {
        $objContainer = Carrier::getInstance()->getContainer();
        $objFactory = new PermissionHandlerFactory($objContainer);

        $objFactory->factory(PermTestModelInvalidHandler::class);
    }
}

/**
 * @permissionHandler perm_test_service
 */
class PermTestModelWithHandler
{
}

class PermTestModelWithoutHandler
{
}

/**
 * @permissionHandler perm_test_service_invalid
 */
class PermTestModelInvalidHandler
{
}

class PermService extends PermissionHandlerAbstract
{
    public function getRoles()
    {
        return [];
    }

    public function getGroupsByRole(Root $objRecord, $strRole)
    {
        return [];
    }

    public function getRoleRights($strRole)
    {
        return [];
    }
}
