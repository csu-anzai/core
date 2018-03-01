<?php

namespace Kajona\System\Tests\Security;

use Kajona\System\System\Lang;
use Kajona\System\System\Security\PasswordValidator;
use Kajona\System\System\Security\Policy\Complexity;
use Kajona\System\System\Security\Policy\MinLength;
use Kajona\System\System\Security\ValidationException;
use Kajona\System\System\UserUser;
use Kajona\System\Tests\Testbase;

class PasswordValidatorTest extends Testbase
{
    /**
     * @dataProvider successProvider
     */
    public function testValidateSuccess($strPassword)
    {
        $objUser = new UserUser();
        $objUser->setStrUsername("foo");

        $this->assertTrue($this->newValidator()->validate($strPassword, $objUser));
    }

    public function successProvider()
    {
        return [
            ["aB0!"],
            ["aB0!abcd"],
        ];
    }

    /**
     * @dataProvider errorProvider
     */
    public function testValidateError($strPassword)
    {
        $objUser = new UserUser();
        $objUser->setStrUsername("foo");

        try {
            $this->newValidator()->validate($strPassword, $objUser);
            $this->fail("Must throw an exception");
        } catch (ValidationException $objE) {
            $this->assertNotEmpty($objE->getMessage());
        }
    }

    public function errorProvider()
    {
        return [
            ["foo"],
            ["fo0"],
            ["foobar0"],
        ];
    }

    protected function newValidator()
    {
        $objValidator = new PasswordValidator(Lang::getInstance());
        $objValidator->addPolicy(new Complexity(1, 1, 1, 1));
        $objValidator->addPolicy(new MinLength(4));

        return $objValidator;
    }
}
