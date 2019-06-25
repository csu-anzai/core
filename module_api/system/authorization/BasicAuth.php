<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Kajona\Api\System\AppContext;
use Kajona\Api\System\AuthorizationInterface;
use Kajona\System\System\Session;
use Slim\Http\Request;

/**
 * BasicAuth
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class BasicAuth implements AuthorizationInterface
{
    /**
     * @inheritdoc
     */
    public function authorize(Request $request, AppContext $context): bool
    {
        $header = explode(" ", $request->getHeaderLine("Authorization"), 2);
        $type = $header[0] ?? null;
        $auth = $header[1] ?? null;

        $parts = explode(":", base64_decode($auth));
        $username = $parts[0] ?? null;
        $password = $parts[1] ?? null;

        if ($type !== "Basic") {
            return false;
        }

        if (!Session::getInstance()->login($username, $password)) {
            return false;
        }

        $context->setUserId(Session::getInstance()->getUserID());

        return true;
    }
}
