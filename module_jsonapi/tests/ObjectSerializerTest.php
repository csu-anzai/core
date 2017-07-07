<?php

namespace Kajona\Jsonapi\Tests;

use Kajona\Jsonapi\System\ObjectSerializer;
use Kajona\System\System\MessagingMessage;
use PHPUnit\Framework\TestCase;

class ObjectSerializerTest extends TestCase
{
    public function testSerialize()
    {
        $objmessage = new MessagingMessage();
        $objSerializer = new ObjectSerializer($objmessage);

        $this->assertEquals(array('strUser', 'strTitle', 'strBody', 'bitRead'), $objSerializer->getPropertyNames());
        $this->assertEquals(array('strUser' => '', 'strTitle' => '', 'strBody' => '', 'bitRead' => 0), $objSerializer->getArrMapping());
    }
}
