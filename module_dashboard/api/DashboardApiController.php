<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace AGP\Dashboard\Api;

use Kajona\Api\System\ApiControllerInterface;
use Kajona\Dashboard\System\ServiceProvider;
use Kajona\System\System\Carrier;
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
     * Returns internet calendar by token
     *
     * @api
     * @method GET
     * @path /caldav/{token}
     */
    public function caldav(HttpContext $context)
    {
        return (Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::DASHBOARD_ICAL_GENERATOR))->generate($context->getUriFragment('token'));
    }

}