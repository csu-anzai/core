<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace AGP\Dashboard\Api;

use Kajona\Api\System\ApiControllerInterface;
use Kajona\Dashboard\System\ICalendar;
use Kajona\System\System\Carrier;
use Kajona\System\System\HttpResponsetypes;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\UserUser;
use PSX\Http\Environment\HttpContext;


/**
 * DashboardApiController
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 */
class DashboardApiController implements ApiControllerInterface
{
    /**
     * inject
     * @var
     */
    protected $icalGenerator;

    /**
     * Returns internet calendar by token
     *
     * @api
     * @method GET
     * @path /caldav/{token}
     */
    public function caldav(HttpContext $context)
    {
        $token = $context->getUriFragment('token');

        $iCal = Objectfactory::getInstance()->getObject($token);
        if (!$iCal instanceof ICalendar) {
            return "";
        }

        $userId = $iCal->getStrUserId();

        $user = Objectfactory::getInstance()->getObject($userId);

        if (!$user instanceof UserUser) {
            return "";
        }

        Carrier::getInstance()->getObjSession()->loginUser($user);
        $icalObject = $iCal->getICalendar();
        Carrier::getInstance()->getObjSession()->logout();

        // Set the headers
        ResponseObject::getInstance()->setStrResponseType(HttpResponsetypes::STR_TYPE_ICAL);
        ResponseObject::getInstance()->addHeader('Content-Disposition: attachment; filename="agpCalendar.ics"');


        return $icalObject;
    }

}