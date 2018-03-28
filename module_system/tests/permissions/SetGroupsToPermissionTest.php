<?php

namespace Kajona\System\Tests\Permissions;

use Kajona\System\System\Permissions\SetGroupsToPermission;
use Kajona\System\System\Rights;
use Kajona\System\System\SystemSetting;
use Kajona\System\Tests\Testbase;

class SetGroupsToPermissionTest extends Testbase
{
    public function testApplyAction()
    {
        $setGroups = new SetGroupsToPermission(generateSystemid(), Rights::$STR_RIGHT_VIEW, ["foo", "bar"]);

        $permissions = [
            Rights::$STR_RIGHT_VIEW => ["bar"],
        ];

        $actual = $setGroups->applyAction($permissions);
        $expect = [
            Rights::$STR_RIGHT_VIEW => ["foo", "bar", SystemSetting::getConfigValue("_admins_group_id_")],
        ];

        $this->assertEquals($expect, $actual);
    }
}
