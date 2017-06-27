<?php

namespace Kajona\System\Tests\Lifecycle;

use Kajona\System\System\Carrier;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Lifecycle\ServiceLifeCycleImpl;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\Tests\Testbase;

class ServiceLifeCycleImplTest extends Testbase
{
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

        $this->objServiceFactory = new ServiceLifeCycleFactory(Carrier::getInstance()->getContainer());
        $this->objServiceImpl = new ServiceLifeCycleImpl($this->objServiceFactory);
    }

    public function testUpdate()
    {
        $strSystemId = generateSystemid();

        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('updateObjectToDb')
            ->with($this->equalTo($strSystemId))
            ->willReturn(true);

        $this->objServiceImpl->update($objModel, $strSystemId);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function testUpdateFailure()
    {
        $strSystemId = generateSystemid();

        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('updateObjectToDb')
            ->with($this->equalTo($strSystemId))
            ->willReturn(false);

        $this->objServiceImpl->update($objModel, $strSystemId);
    }

    public function testDelete()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('deleteObject')
            ->willReturn(true);

        $this->objServiceImpl->delete($objModel);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleLogicDeleteException
     */
    public function testDeleteFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('deleteObject')
            ->willReturn(false);

        $this->objServiceImpl->delete($objModel);
    }

    public function testDeleteObjectFromDatabase()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('deleteObjectFromDatabase')
            ->willReturn(true);

        $this->objServiceImpl->deleteObjectFromDatabase($objModel);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleDeleteException
     */
    public function testDeleteObjectFromDatabaseFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('deleteObjectFromDatabase')
            ->willReturn(false);

        $this->objServiceImpl->deleteObjectFromDatabase($objModel);
    }

    public function testRestore()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('restoreObject')
            ->willReturn(true);

        $this->objServiceImpl->restore($objModel);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleRestoreException
     */
    public function testRestoreFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('restoreObject')
            ->willReturn(false);

        $this->objServiceImpl->restore($objModel);
    }

    public function testCopy()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('copyObject')
            ->willReturn(true);

        $this->objServiceImpl->copy($objModel);
    }

    /**
     * @expectedException \Kajona\System\System\Lifecycle\ServiceLifeCycleCopyException
     */
    public function testCopyFailure()
    {
        $objModel = $this->createMock(DummyModel::class);
        $objModel->method('copyObject')
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

