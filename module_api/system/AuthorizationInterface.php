<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System;

use Kajona\System\System\Exception;
use Slim\Http\Request;

/**
 * AuthorizationInterface
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
interface AuthorizationInterface
{
    /**
     * Validates the authorization header and adds optional specific attributes to the app context i.e. in case we can
     * determine a user id. Returns a boolean indicating whether the request is authorized or not
     *
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function authorize(Request $request): bool;
}
