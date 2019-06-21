<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Date;
use Kajona\System\System\DateFormatter;
use Kajona\System\System\Lang;

class CreateFilenameTest extends Testbase
{
    public function testFilename()
    {
        $this->assertEquals("Test.doc", createFilename("Test.doc"));
        $this->assertEquals("Test.doc", createFilename("Test\n.doc"));
        $this->assertEquals("Teaest.doc", createFilename("Teäst\n.doc"));
        $this->assertEquals("Te st.doc", createFilename("Te st.doc"));
    }
}
