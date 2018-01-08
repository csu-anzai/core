<?php

namespace Kajona\System\Tests\Security\Policy;

use Kajona\System\System\Security\Policy\MinLength;
use Kajona\System\Tests\Testbase;

class MinLengthTest extends Testbase
{
    /**
     * @dataProvider successProvider
     */
    public function testValidateSuccess($strPassword)
    {
        $this->assertTrue($this->newPolicy()->validate($strPassword));
    }

    public function successProvider()
    {
        return [
            ["aaaaaaaa"],
            ["aaaaaaaaa"],
        ];
    }

    /**
     * @dataProvider errorProvider
     */
    public function testValidateError($strPassword)
    {
        $this->assertFalse($this->newPolicy()->validate($strPassword));
    }

    public function errorProvider()
    {
        return [
            ["aaaaaaa"],
            ["a"],
            [""],
        ];
    }

    protected function newPolicy()
    {
        return new MinLength(8);
    }
}
