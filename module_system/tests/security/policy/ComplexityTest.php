<?php

namespace Kajona\System\Tests\Security\Policy;

use Kajona\System\System\Security\Policy\Complexity;
use Kajona\System\Tests\Testbase;

class ComplexityTest extends Testbase
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
            ["bAr9!"],
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
            ["foo"],
            ["foO"],
            ["fo0"],
            ["fO0"],
        ];
    }

    protected function newPolicy()
    {
        return new Complexity(1, 1, 1, 1);
    }
}
