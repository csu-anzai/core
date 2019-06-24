<?php

declare(strict_types=1);

namespace Kajona\System\Tests;

class CreateFilenameTest extends Testbase
{
    public function testStripsVerticalWhitespace(): void
    {
        $this->assertSame("Test.doc", createFilename("Test.doc"));
        $this->assertSame("Test.doc", createFilename("Test\n.doc"));
    }

    public function testStripsUmlautsAndWhitespace(): void
    {
        $this->assertSame("Teaest.doc", createFilename("Teäst\n.doc"));
    }

    public function testStripsWhitespace(): void
    {
        $this->assertSame("Test .doc", createFilename("Test\t.doc"));
        $this->assertSame("Tes t.doc", createFilename("Tes\tt.doc"));
        $this->assertSame("Te st.doc", createFilename("Te st.doc"));
    }
}
