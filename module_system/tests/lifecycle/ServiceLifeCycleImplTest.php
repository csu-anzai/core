<?php

namespace Kajona\System\Tests\Lifecycle;

use Kajona\System\System\Carrier;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Lifecycle\ServiceLifeCycleImpl;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\Permissions\PermissionHandlerFactory;
use Kajona\System\System\Root;
use Kajona\System\System\ServiceProvider;
use Kajona\System\Tests\Testbase;

class ServiceLifeCycleImplTest extends Testbase
{
    /**
     * @var PermissionHandlerFactory
     */
    protected $objPermissionHandlerFactory;

    /**
     * @var ServiceLifeCycleFactory
     */
    protected $objServiceFactory;

    /**
     * @var ServiceLifeCycleImpl
     */
    protected $objServiceImpl;

    protected function setUp()
    {
        parent::setUp();

        $this->objPermissionHandlerFactory = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_PERMISSION_HANDLER_FACTORY);
        $this->objServiceFactory = new ServiceLifeCycleFactory(Carrier::getInstance()->getContainer());
        $this->objServiceImpl = new ServiceLifeCycleImpl($this->objPermissionHandlerFactory);
    }

    public function testUpdate()
    {
        $strSystemId = generateSystemid();

        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('updateObjectToDb')
            ->with($this->equalTo($strSystemId))
            ->willReturn(true);

        /** @var $objModel Root */
        $result = $this->objServiceImpl->update($objModel, $strSystemId);

        $this->assertEmpty($result);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function testUpdateFailure()
    {
        $strSystemId = generateSystemid();

        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('updateObjectToDb')
            ->with($this->equalTo($strSystemId))
            ->willReturn(false);

        $this->objServiceImpl->update($objModel, $strSystemId);
    }

    public function testDelete()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('deleteObject')
            ->willReturn(true);

        /** @var $objModel Root */
        $result = $this->objServiceImpl->delete($objModel);

        $this->assertEmpty($result);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleLogicDeleteException
     */
    public function testDeleteFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('deleteObject')
            ->willReturn(false);

        $this->objServiceImpl->delete($objModel);
    }

    public function testDeleteObjectFromDatabase()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('deleteObjectFromDatabase')
            ->willReturn(true);

        /** @var $objModel Root */
        $result = $this->objServiceImpl->deleteObjectFromDatabase($objModel);

        $this->assertEmpty($result);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleDeleteException
     */
    public function testDeleteObjectFromDatabaseFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('deleteObjectFromDatabase')
            ->willReturn(false);

        $this->objServiceImpl->deleteObjectFromDatabase($objModel);
    }

    public function testRestore()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('restoreObject')
            ->willReturn(true);

        /** @var $objModel Root */
        $result = $this->objServiceImpl->restore($objModel);

        $this->assertEmpty($result);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleRestoreException
     */
    public function testRestoreFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('restoreObject')
            ->willReturn(false);

        $this->objServiceImpl->restore($objModel);
    }

    public function testCopy()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('copyObject')
            ->willReturn(true);

        $result = $this->objServiceImpl->copy($objModel);

        $this->assertInstanceOf(Root::class, $result);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleCopyException
     */
    public function testCopyFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->expects($this->once())
            ->method('copyObject')
            ->willReturn(false);

        $this->objServiceImpl->copy($objModel);
    }
}

class DummyModel extends Model implements ModelInterface
{
    public function getStrDisplayName()
    {
        return "test";
    }
}

