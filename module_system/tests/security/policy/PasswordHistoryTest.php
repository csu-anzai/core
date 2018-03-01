<?php

namespace Kajona\System\Tests\Security\Policy;

use Kajona\System\System\Security\Policy\PasswordHistory;
use Kajona\System\System\UserUser;
use Kajona\System\Tests\Testbase;

class PasswordHistoryTest extends Testbase
{
    public function testValidateSuccess()
    {
        $objUser = new UserUser();
        $objUser->setStrUsername("foo");

        $this->assertTrue($this->newPolicy($objUser, true)->validate("foo", $objUser));
    }

    public function testValidateError()
    {
        $objUser = new UserUser();
        $objUser->setStrUsername("foo");

        $this->assertFalse($this->newPolicy($objUser, false)->validate("foo", $objUser));
    }

    /**
     * @param UserUser $objUser
     * @param bool $bitReturn
     * @return PasswordHistory
     */
    protected function newPolicy(UserUser $objUser, $bitReturn)
    {
        $objMock = $this->getMockBuilder(PasswordHistory::class)
            ->setMethods(["hasPasswordNotUsed"])
            ->getMock();

        $objMock->expects($this->once())
            ->method("hasPasswordNotUsed")
            ->with($this->equalTo("foo"), $this->equalTo($objUser))
            ->will($this->returnValue($bitReturn));

        return $objMock;
    }
}
