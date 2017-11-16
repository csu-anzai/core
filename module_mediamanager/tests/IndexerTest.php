<?php

namespace Kajona\Mediamanager\Tests;

use Kajona\Mediamanager\System\Search\Indexer;
use Kajona\System\Tests\Testbase;

class IndexerTest extends Testbase
{
    /**
     * @var Indexer
     */
    protected $objIndexer;

    protected function setUp()
    {
        parent::setUp();

        $this->objIndexer = new Indexer();
    }

    /**
     * @dataProvider getFileProvider
     */
    public function testGet($strPath, $strExpect)
    {
        $this->assertEquals($strExpect, $this->objIndexer->get(__DIR__ . "/files/" . $strPath));
    }

    public function getFileProvider()
    {
        return [
            ["test.doc", "oo"],
            ["test.docx", "Foo Bar"],
            ["test.pdf", "Foo Bar"],
            ["test.ppt", "Foo bar"],
            ["test.pptx", "Foo bar"],
            ["test.xls", "foo bar"],
            ["test.xlsx", "foo bar"],
            ["test.foo", null],
        ];
    }
}
