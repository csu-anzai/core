<?php

namespace Kajona\System\Tests\Permissions;

use Kajona\Flow\System\FlowManager;
use Kajona\Flow\System\FlowStatus;
use Kajona\System\System\Carrier;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Permissions\PermissionHandlerAbstract;
use Kajona\System\System\Rights;
use Kajona\System\System\Root;
use Kajona\System\System\ServiceProvider;
use Kajona\System\System\SystemModule;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\UserGroup;
use Kajona\System\Tests\Testbase;

/**
 * @author christoph.kappestein@artemeon.de
 */
class PermissionHandlerAbstractTest extends Testbase
{
    const GROUP_RESP = "PermResp";
    const GROUP_COMPLIANCE = "PermCompliance";

    protected $strGroupRespId;
    protected $strGroupComplianceId;
    protected $strGroupAdminId;

    public function setUp()
    {
        parent::setUp();

        $objGroupResp = UserGroup::getGroupByName(self::GROUP_RESP);
        if (!$objGroupResp) {
            $objGroupResp = new UserGroup();
            $objGroupResp->setStrName(self::GROUP_RESP);
            $objGroupResp->updateObjectToDb();
        }

        $objGroupCompliance = UserGroup::getGroupByName(self::GROUP_COMPLIANCE);
        if (!$objGroupCompliance) {
            $objGroupCompliance = new UserGroup();
            $objGroupCompliance->setStrName(self::GROUP_COMPLIANCE);
            $objGroupCompliance->updateObjectToDb();
        }

        $this->strGroupRespId = $objGroupResp->getSystemid();
        $this->strGroupComplianceId = $objGroupCompliance->getSystemid();
        $this->strGroupAdminId = SystemSetting::getConfigValue("_admins_group_id_");
    }

    public function tearDown()
    {
        parent::tearDown();

        $objGroupResp = Objectfactory::getInstance()->getObject($this->strGroupRespId);
        if ($objGroupResp instanceof UserGroup) {
            $objGroupResp->deleteObjectFromDatabase();
        }

        $objGroupCompliance = Objectfactory::getInstance()->getObject($this->strGroupComplianceId);
        if ($objGroupCompliance instanceof UserGroup) {
            $objGroupCompliance->deleteObjectFromDatabase();
        }
    }

    public function testOnCreate()
    {
        $objRecord = new SystemModule();

        $objHandler = $this->newHandler($this->getConfiguredRoles(), $this->getExpectedRights(), true, true);
        $objHandler->onCreate($objRecord);
    }

    public function testOnUpdate()
    {
        $objOldRecord = new SystemModule();
        $objOldRecord->setIntRecordStatus(1);
        $objNewRecord = new SystemModule();
        $objNewRecord->setIntRecordStatus(2);

        $objHandler = $this->newHandler($this->getConfiguredRoles(), $this->getExpectedRights(), true, true);
        $objHandler->onUpdate($objOldRecord, $objNewRecord);
    }

    public function testOnUpdateNoChange()
    {
        $objOldRecord = new SystemModule();
        $objNewRecord = new SystemModule();

        $objHandler = $this->newHandler($this->getConfiguredRoles(), $this->getExpectedRights(), false, false);
        $objHandler->onUpdate($objOldRecord, $objNewRecord);
    }

    public function testCalculatePermissions()
    {
        $objRecord = new SystemModule();

        $objHandler = $this->newHandler($this->getConfiguredRoles(), $this->getExpectedRights(), true, true);
        $objHandler->calculatePermissions($objRecord);
    }

    public function testCalculatePermissionsNullRoles()
    {
        $objRecord = new SystemModule();

        $objHandler = $this->newHandler(null, $this->getExpectedRights(), true, false);
        $objHandler->calculatePermissions($objRecord);
    }

    public function testCalculatePermissionsEmptyRoles()
    {
        $objRecord = new SystemModule();

        $objHandler = $this->newHandler([], $this->getExpectedRights(), true, false);
        $objHandler->calculatePermissions($objRecord);
    }

    private function newHandler(array $arrRoles = null, array $arrExpectRights = [], $expectFlowCall = true, $expectRightsCall = true)
    {
        $container = Carrier::getInstance()->getContainer();

        $objStatus = new FlowStatus();
        if ($arrRoles !== null) {
            $objStatus->setRoles($arrRoles);
        }

        $objFlowManager = $this->getMockBuilder(FlowManager::class)
            ->setMethods(["getCurrentStepForModel"])
            ->getMock();

        if ($expectFlowCall) {
            $objFlowManager->expects($this->once())
                ->method("getCurrentStepForModel")
                ->willReturn($objStatus);
        } else {
            $objFlowManager->expects($this->never())
                ->method("getCurrentStepForModel");
        }

        $objRights = $this->getMockBuilder(Rights::class)
            ->setMethods(["setRights"])
            ->getMock();

        if ($expectRightsCall) {
            $objRights->expects($this->once())
                ->method("setRights")
                ->with($this->equalTo($arrExpectRights));
        } else {
            $objRights->expects($this->never())
                ->method("setRights");
        }

        return new PermTestHandler(
            $container[ServiceProvider::STR_OBJECT_FACTORY],
            $objRights,
            $objFlowManager
        );
    }

    private function getExpectedRights()
    {
        $permissionRow = [
            'view' => [$this->strGroupAdminId, $this->strGroupRespId, $this->strGroupComplianceId],
            'edit' => [$this->strGroupAdminId, $this->strGroupRespId],
            'delete' => [$this->strGroupAdminId],
            'right' => [$this->strGroupAdminId],
            'right1' => [$this->strGroupAdminId],
            'right2' => [$this->strGroupAdminId],
            'right3' => [$this->strGroupAdminId],
            'right4' => [$this->strGroupAdminId, $this->strGroupComplianceId],
            'right5' => [$this->strGroupAdminId],
            'changelog' => [$this->strGroupAdminId],
            'inherit' => 0,
        ];

        $objRights = Rights::getInstance();
        $arrExpect = $objRights->convertSystemidArrayToShortIdString($permissionRow);

        return $arrExpect;
    }

    private function getConfiguredRoles()
    {
        return [
            PermTestHandler::ROLE_RESPONSIBLE => ["view", "edit"],
            PermTestHandler::ROLE_COMPLIANCE => ["view", "right4"],
            PermTestHandler::ROLE_ADMIN => ["view", "edit", "delete", "right4"],
        ];
    }
}

class PermTestHandler extends PermissionHandlerAbstract
{
    const ROLE_RESPONSIBLE = 1;
    const ROLE_COMPLIANCE = 2;
    const ROLE_ADMIN = 3;

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return [
            self::ROLE_RESPONSIBLE,
            self::ROLE_COMPLIANCE,
            self::ROLE_ADMIN,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGroupsByRole(Root $objRecord, $strRole)
    {
        $arrGroups = [];
        if ($strRole == self::ROLE_RESPONSIBLE) {
            $arrGroups[] = UserGroup::getGroupByName(PermissionHandlerAbstractTest::GROUP_RESP);
        } elseif ($strRole == self::ROLE_COMPLIANCE) {
            $arrGroups[] = UserGroup::getGroupByName(PermissionHandlerAbstractTest::GROUP_COMPLIANCE);
        } elseif ($strRole == self::ROLE_ADMIN) {
            $arrGroups[] = Objectfactory::getInstance()->getObject(SystemSetting::getConfigValue("_admins_group_id_"));
        }

        $arrResult = [];
        foreach ($arrGroups as $objGroup) {
            if ($objGroup instanceof UserGroup) {
                $arrResult[$objGroup->getSystemid()] = $objGroup;
            }
        }

        return $arrResult;
    }

    /**
     * @inheritdoc
     */
    public function getRoleRights($strRole)
    {
        return [
            Rights::$STR_RIGHT_VIEW => "View",
            Rights::$STR_RIGHT_EDIT => "Edit",
            Rights::$STR_RIGHT_DELETE => "Delete",
            Rights::$STR_RIGHT_RIGHT4 => "Status",
        ];
    }
}

