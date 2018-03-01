<?php

namespace Kajona\System\Tests\Security\Policy;

use Kajona\System\System\Security\Policy\Blacklist;
use Kajona\System\Tests\Testbase;

class BlacklistTest extends Testbase
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
            ["bar"],
            ["fobar"],
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
            ["FOO"],
            ["fOo"],
            ["barfoobar"],
            ["foobar"],
            ["barfoo"],
        ];
    }

    protected function newPolicy()
    {
        return new Blacklist(["foo"]);
    }
}
