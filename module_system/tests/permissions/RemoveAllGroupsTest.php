<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Permissions\RemoveAllGroups;
use Kajona\System\System\Rights;
use Kajona\System\System\SystemSetting;

class RemoveAllGroupsTest extends Testbase
{
    public function testApplyAction()
    {
        $groupId = generateSystemid();
        $removeGroup = new RemoveAllGroups(generateSystemid());

        $permissions = [
            Rights::$STR_RIGHT_VIEW => [$groupId, "foo", "bar"],
            Rights::$STR_RIGHT_EDIT => ["foo", $groupId, "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar", $groupId],
        ];

        $actual = $removeGroup->applyAction($permissions);
        $expect = [
            Rights::$STR_RIGHT_VIEW => [SystemSetting::getConfigValue("_admins_group_id_")],
            Rights::$STR_RIGHT_EDIT => [SystemSetting::getConfigValue("_admins_group_id_")],
            Rights::$STR_RIGHT_DELETE => [SystemSetting::getConfigValue("_admins_group_id_")],
        ];

        $this->assertEquals($expect, $actual);
    }
}
