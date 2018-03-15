<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Permissions\RemoveGroup;
use Kajona\System\System\Rights;

class RemoveGroupTest extends Testbase
{
    public function testApplyAction()
    {
        $groupId = generateSystemid();
        $removeGroup = new RemoveGroup(generateSystemid(), $groupId);

        $permissions = [
            Rights::$STR_RIGHT_VIEW => [$groupId, "foo", "bar"],
            Rights::$STR_RIGHT_EDIT => ["foo", $groupId, "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar", $groupId],
        ];

        $actual = $removeGroup->applyAction($permissions);
        $expect = [
            Rights::$STR_RIGHT_VIEW => ["foo", "bar"],
            Rights::$STR_RIGHT_EDIT => ["foo", "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar"],
        ];

        $this->assertEquals($expect, $actual);
    }
}
