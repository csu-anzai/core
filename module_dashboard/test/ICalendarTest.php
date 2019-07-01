<?php

namespace AGP\Dashboard\Tests;

use Kajona\Dashboard\System\EventEntry;
use Kajona\Dashboard\System\ICalendar;
use Kajona\System\System\Date;
use Kajona\System\Tests\Testbase;


class ICalendarTest extends Testbase
{
    /**
     * Test ICalendar generator output
     * @throws \Kajona\System\System\Exception
     */
    public function testICalendarGenerator()
    {
        $events = [];
        $event = new EventEntry();
        $event->setStrDisplayName("Test event 01");
        $event->setObjStartDate(new Date(strtotime('01.01.2019 16:00')));
        $event->setObjEndDate(new Date(strtotime('01.01.2019 18:00')));
        $events[] = $event;
        $event = new EventEntry();
        $event->setStrDisplayName("Test event 02");
        $event->setObjValidDate(new Date(strtotime('02.01.2019')));
        $events[] = $event;
        $iCal = new ICalendar();
        $iCalendar = $iCal->generate($events);
        $this->assertContains("BEGIN:VCALENDAR", $iCalendar);
        $this->assertContains("END:VCALENDAR", $iCalendar);
        $this->assertContains("SUMMARY:Test event 01", $iCalendar);
        $this->assertContains("DTSTART:20190101T160000Z", $iCalendar);
        $this->assertContains("DTEND:20190101T180000Z", $iCalendar);
        $this->assertContains("SUMMARY:Test event 02", $iCalendar);
        $this->assertContains("DTSTART:20190102T000000", $iCalendar);
        $this->assertContains("DTEND:20190102T000000", $iCalendar);
    }
}
