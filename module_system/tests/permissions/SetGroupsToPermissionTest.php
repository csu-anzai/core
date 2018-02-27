<?php

namespace Kajona\System\Tests;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;
use Kajona\System\System\Permissions\ReplaceGroup;
use Kajona\System\System\Permissions\SetGroupsToPermission;
use Kajona\System\System\Rights;

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
            Rights::$STR_RIGHT_VIEW => ["foo", "bar"],
        ];

        $this->assertEquals($expect, $actual);
    }
}
