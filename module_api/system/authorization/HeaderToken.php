<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Slim\Http\Request;

/**
 * Authorization service where the user can provide the access token as Authorization header
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class HeaderToken extends UserTokenAbstract
{
    /**
     * @inheritDoc
     */
    protected function getToken(Request $request): ?string
    {
        $header = explode(" ", $request->getHeaderLine("Authorization"), 2);
        $type = $header[0] ?? null;
        $token = $header[1] ?? null;

        return $type === "Bearer" ? $token : null;
    }
}
