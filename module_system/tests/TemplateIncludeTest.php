<?php

namespace Kajona\System\Tests;

use Kajona\System\System\AdminskinHelper;
use Kajona\System\System\Filesystem;
use Kajona\System\System\Session;
use Kajona\System\System\TemplateFileParser;

class TemplateIncludeTest extends Testbase
{

    public function testTemplateIncludes()
    {

        $objFilesystem = new Filesystem();

        file_put_contents(_realpath_ . "core/module_v4skin/admin/skins/kajona_v4/test1.tpl", "
            page template

            [KajonaTemplateInclude,"._realpath_ . "core/module_v4skin/admin/skins/kajona_v4/test2.tpl]
        ");

        $this->assertFileExists(_realpath_ . "core/module_v4skin/admin/skins/kajona_v4/test1.tpl");


        file_put_contents(_realpath_ . "core/module_v4skin/admin/skins/kajona_v4/test2.tpl", "template 2");

        $this->assertFileExists(_realpath_ . "core/module_v4skin/admin/skins/kajona_v4/test2.tpl");


        $objParser = new TemplateFileParser();
        $strContent = $objParser->readTemplate(_realpath_ . "core/module_v4skin/admin/skins/kajona_v4/test1.tpl");

        $this->assertEquals($strContent, "
            page template

            template 2
        ");

        $objFilesystem->fileDelete("/core/module_v4skin/admin/skins/kajona_v4/test1.tpl");
        $objFilesystem->fileDelete("/core/module_v4skin/admin/skins/kajona_v4/test2.tpl");
    }


}

