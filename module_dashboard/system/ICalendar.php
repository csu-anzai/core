<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Dashboard\System;

use Kajona\System\System\Date;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;

/**
 * Object which represents a iCal entry
 *
 * @package module_dashboard
 * @author andrii.konoval@artemeon.de
 * @targetTable agp_dashboard_ical.ical_id
 *
 * @module dashboard
 * @moduleId _dashboard_module_id_
 */
class ICalendar extends Model implements ModelInterface
{

    const ICAL_START = '-3 month';
    const ICAL_END = '+1 year';
    const ICAL_VALID_TIME = 15;
    const ICAL_LONG_FORMAT = 'Ymd\THis\Z';
    const ICAL_SHORT_FORMAT = 'Ymd\T000000';

    /**
     * @var string
     * @tableColumn agp_dashboard_ical.user_systemid
     * @tableColumnDatatype char20
     */
    private $strUserId = "";

    /**
     * @var string
     * @tableColumn agp_dashboard_ical.cache
     * @tableColumnDatatype longtext
     */
    private $strICalCache = "";

    /**
     * @var int
     * @tableColumn agp_dashboard_ical.create_date
     */
    private $longCreateDate = null;

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getStrUserId()
    {
        return $this->strUserId;
    }

    /**
     * @param string $strUserId
     */
    public function setStrUserId($strUserId)
    {
        $this->strUserId = $strUserId;
    }

    /**
     * @return string
     */
    public function getStrICalCache()
    {
        return $this->strICalCache;
    }

    /**
     * @param string $strICalCache
     */
    public function setStrICalCache($strICalCache)
    {
        $this->strICalCache = $strICalCache;
    }

    /**
     * @return int
     */
    public function getLongCreateDate()
    {
        return $this->longCreateDate;
    }

    /**
     * @param Date $longCreateDate
     */
    public function setLongCreateDate($longCreateDate)
    {
        $this->longCreateDate = $longCreateDate;
    }

    /**
     * @param EventEntry[] $events
     * @return string
     */
    public function generate(array $events): string
    {
        $icalObject = <<<ICALHEADER
BEGIN:VCALENDAR
VERSION:2.0
METHOD:PUBLISH
PRODID:-//AGP Events//DE\n
ICALHEADER;

        foreach ($events as $event) {
            if ($event->getObjStartDate() instanceof Date && $event->getObjEndDate() instanceof Date) {
                $eventStartDate = date(self::ICAL_LONG_FORMAT, $event->getObjStartDate()->getTimeInOldStyle());
                $eventEndDate = date(self::ICAL_LONG_FORMAT, $event->getObjEndDate()->getTimeInOldStyle());
            } elseif ($event->getObjValidDate() instanceof Date) {
                $eventStartDate = date(self::ICAL_SHORT_FORMAT, $event->getObjValidDate()->getTimeInOldStyle());
                $eventEndDate = date(self::ICAL_SHORT_FORMAT, $event->getObjValidDate()->getTimeInOldStyle());
            } else {
                continue;
            }
            $summary = strip_tags($event->getStrDisplayName());
            $description = $event->getStrHref();
            $icalObject .= <<<ICALBODY
BEGIN:VEVENT
DTSTART:$eventStartDate
DTEND:$eventEndDate
SUMMARY:$summary
DESCRIPTION:$description
END:VEVENT\n
ICALBODY;
        }
        $icalObject .= "END:VCALENDAR";

        return $icalObject;
    }

}
