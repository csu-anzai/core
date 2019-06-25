<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System;

use Slim\Http\Request;

/**
 * AuthorizationInterface
 *
 * @author christoph.kappestein@gmail.com
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
     */
    public function authorize(Request $request): bool;
}
