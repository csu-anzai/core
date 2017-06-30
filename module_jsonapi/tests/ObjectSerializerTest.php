<?php

namespace Kajona\Jsonapi\Tests;

use Kajona\Jsonapi\System\ObjectSerializer;
use Kajona\News\System\NewsNews;
use Kajona\System\System\Date;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\SystemAspect;

class ObjectSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $objmessage = new MessagingMessage();
        $objSerializer = new ObjectSerializer($objmessage);

        $this->assertEquals(array('strUser', 'strTitle', 'strBody', 'bitRead'), $objSerializer->getPropertyNames());
        $this->assertEquals(array('strUser' => '', 'strTitle' => '', 'strBody' => '', 'bitRead' => 0), $objSerializer->getArrMapping());
    }
}
