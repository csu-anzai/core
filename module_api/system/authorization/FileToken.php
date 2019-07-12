<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Kajona\Api\System\AuthorizationInterface;
use Kajona\Api\System\ProjectSecret;
use Slim\Http\Request;

/**
 * Simple authorization service which reads a static token on the filesystem and requires this token for every request.
 * Useful for i.e. the installer where we dont have a working database
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class FileToken implements AuthorizationInterface
{
    /**
     * @var ProjectSecret
     */
    private $projectSecret;

    /**
     * @param ProjectSecret $projectSecret
     */
    public function __construct(ProjectSecret $projectSecret)
    {
        $this->projectSecret = $projectSecret;
    }

    /**
     * @inheritdoc
     */
    public function isAuthorized(Request $request): bool
    {
        $header = explode(" ", $request->getHeaderLine("Authorization"), 2);
        $type = $header[0] ?? null;
        $token = $header[1] ?? null;

        if ($type !== "Bearer") {
            return false;
        }

        if ($token !== $this->projectSecret->getToken()) {
            return false;
        }

        return true;
    }
}