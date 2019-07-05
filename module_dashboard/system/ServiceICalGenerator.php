<?php

namespace Kajona\Dashboard\System;

use Kajona\System\System\Carrier;
use Kajona\System\System\HttpResponsetypes;
use Kajona\System\System\HttpStatuscodes;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\UserUser;

/**
 * ServiceICalGenerator
 *
 * @package Kajona\System\System
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 */
class ServiceICalGenerator
{
    /**
     * @param string $token
     * @return string
     * @throws \Kajona\System\System\Exception
     */
    public function generate(string $token): string
    {
        $response = ResponseObject::getInstance();

        if (!validateSystemid($token)) {
            $response->setStrStatusCode(HttpStatuscodes::SC_BADREQUEST);
            $response->sendHeaders();
            return "";
        }

        $iCal = Objectfactory::getInstance()->getObject($token);
        if (!$iCal instanceof ICalendar) {
            $response->setStrStatusCode(HttpStatuscodes::SC_NOT_FOUND);
            $response->sendHeaders();
            return "";
        }

        $userId = $iCal->getStrUserId();

        $user = Objectfactory::getInstance()->getObject($userId);

        if (!$user instanceof UserUser) {
            ResponseObject::getInstance()->setStrStatusCode(HttpStatuscodes::SC_NOT_FOUND);
            ResponseObject::getInstance()->sendHeaders();
            return "";
        }

        Carrier::getInstance()->getObjSession()->loginUser($user);
        $calDavCalendar = $iCal->getICalendar();
        Carrier::getInstance()->getObjSession()->logout();

        // Set the headers
        $response->setStrResponseType(HttpResponsetypes::STR_TYPE_ICAL);
        $response->addHeader('Content-Disposition: attachment; filename="agpCalendar.ics"');

        return $calDavCalendar;
    }

}
