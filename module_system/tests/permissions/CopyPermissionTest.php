<?php

namespace Kajona\System\Tests\Permissions;

use Kajona\System\System\Permissions\CopyPermission;
use Kajona\System\System\Rights;
use Kajona\System\System\SystemModule;
use Kajona\System\Tests\Testbase;

class CopyPermissionTest extends Testbase
{
    public function testApplyAction()
    {
        $rightManager = Rights::getInstance();
        $foreignId = SystemModule::getModuleByName("system")->getSystemid();
        $rights = $rightManager->getArrayRights($foreignId, Rights::$STR_RIGHT_VIEW);
        $copyPermission = new CopyPermission($rightManager, generateSystemid(), $foreignId, Rights::$STR_RIGHT_VIEW);

        $permissions = [
            Rights::$STR_RIGHT_VIEW => ["foo", "bar"],
            Rights::$STR_RIGHT_EDIT => ["foo", "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar"],
        ];

        $actual = $copyPermission->applyAction($permissions);
        $expect = [
            Rights::$STR_RIGHT_VIEW => $rights[Rights::$STR_RIGHT_VIEW],
            Rights::$STR_RIGHT_EDIT => ["foo", "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar"],
        ];

        $this->assertEquals($expect, $actual);
    }
}
