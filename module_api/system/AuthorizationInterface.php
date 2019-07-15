<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System;

use Kajona\System\System\Exception;
use Slim\Http\Request;

/**
 * Implemented by services which can authorize an incoming request. Those services can be used at your api controller
 * by using the "authorization" annotation. Then every incoming request is checked with this service
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
interface AuthorizationInterface
{
    /**
     * Returns a boolean indicating whether the request is authorized or not
     *
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function isAuthorized(Request $request): bool;
}
