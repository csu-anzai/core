<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Kajona\Api\System\AuthorizationInterface;
use Kajona\Api\System\TokenReader;
use Slim\Http\Request;

/**
 * Simple authorization service which reads a static token on the filesystem and requires this token for every request
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class FileToken implements AuthorizationInterface
{
    /**
     * @var TokenReader
     */
    private $tokenReader;

    /**
     * @param TokenReader $tokenReader
     */
    public function __construct(TokenReader $tokenReader)
    {
        $this->tokenReader = $tokenReader;
    }

    /**
     * @inheritdoc
     */
    public function authorize(Request $request): bool
    {
        $header = explode(" ", $request->getHeaderLine("Authorization"), 2);
        $type = $header[0] ?? null;
        $token = $header[1] ?? null;

        if ($type !== "Bearer") {
            return false;
        }

        if ($token !== $this->tokenReader->getToken()) {
            return false;
        }

        return true;
    }
}