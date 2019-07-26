<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Slim\Http\Request;

/**
 * Authorization service where the user can provide the access token as query parameter
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class QueryToken extends UserTokenAbstract
{
    /**
     * @inheritDoc
     */
    protected function getToken(Request $request): ?string
    {
        return $request->getQueryParam("access_token");
    }
}
