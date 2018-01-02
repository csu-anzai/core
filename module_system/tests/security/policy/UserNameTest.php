<?php

namespace Kajona\System\Tests\Security\Policy;

use Kajona\System\System\Security\Policy\UserName;
use Kajona\System\System\UserUser;
use Kajona\System\Tests\Testbase;

class UserNameTest extends Testbase
{
    /**
     * @dataProvider successProvider
     */
    public function testValidateSuccess($strPassword)
    {
        $objUser = new UserUser();
        $objUser->setStrUsername("foo");

        $this->assertTrue($this->newPolicy()->validate($strPassword, $objUser));
    }

    public function successProvider()
    {
        return [
            ["bar"],
        ];
    }

    /**
     * @dataProvider errorProvider
     */
    public function testValidateError($strPassword)
    {
        $objUser = new UserUser();
        $objUser->setStrUsername("foo");

        $this->assertFalse($this->newPolicy()->validate($strPassword, $objUser));
    }

    public function errorProvider()
    {
        return [
            ["foobar"],
            ["foo"],
            ["dafo"],
        ];
    }

    protected function newPolicy()
    {
        return new UserName();
    }
}
