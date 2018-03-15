<?php

namespace Kajona\System\Tests;

use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\System\Model;
use Kajona\System\System\Permissions\ReplaceGroup;
use Kajona\System\System\Rights;

class ReplaceGroupTest extends Testbase
{
    public function testApplyAction()
    {
        $oldGroupId = generateSystemid();
        $newGroupId = generateSystemid();
        $removeGroup = new ReplaceGroup(generateSystemid(), $oldGroupId, $newGroupId);

        $permissions = [
            Rights::$STR_RIGHT_VIEW => [$oldGroupId, "foo", "bar"],
            Rights::$STR_RIGHT_EDIT => ["foo", $oldGroupId, "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar", $oldGroupId],
        ];

        $actual = $removeGroup->applyAction($permissions);
        $expect = [
            Rights::$STR_RIGHT_VIEW => [$newGroupId, "foo", "bar"],
            Rights::$STR_RIGHT_EDIT => ["foo", $newGroupId, "bar"],
            Rights::$STR_RIGHT_DELETE => ["foo", "bar", $newGroupId],
        ];

        $this->assertEquals($expect, $actual);
    }
}
