<?php

namespace Kajona\System\Tests;

use Kajona\System\System\DropdownConfigLoader;

class DropdownConfigLoaderTest extends Testbase
{
    public function testFetchValues()
    {
        $loader = new DropdownConfigLoader();
        $values = $loader->fetchValues("password_validator", ["module" => "module_system"]);

        $this->assertTrue(is_array($values));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Dropdown provider annotation has no module parameter
     */
    public function testFetchValuesNoModule()
    {
        $loader = new DropdownConfigLoader();
        $loader->fetchValues("password_validator", []);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Dropdown provider config value must be an array
     */
    public function testFetchValuesInvalidModule()
    {
        $loader = new DropdownConfigLoader();
        $loader->fetchValues("password_validator", ["module" => "foo"]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Dropdown provider config value must be an array
     */
    public function testFetchValuesInvalidKey()
    {
        $loader = new DropdownConfigLoader();
        $loader->fetchValues("foo", ["module" => "module_system"]);
    }
}
