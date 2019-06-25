<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Firebase\JWT\JWT;
use Kajona\Api\System\AuthorizationInterface;
use Kajona\Api\System\TokenReader;
use Kajona\System\System\Database;
use Kajona\System\System\Exception;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Session;
use Kajona\System\System\UserUser;
use Slim\Http\Request;

/**
 * Authorization service which uses the access token for a user
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class UserToken implements AuthorizationInterface
{
    const JWT_ALG = 'HS256';

    /**
     * @var Database
     */
    private $connection;

    /**
     * @var TokenReader
     */
    private $tokenReader;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Database $connection
     * @param TokenReader $tokenReader
     * @param Session $session
     */
    public function __construct(Database $connection, TokenReader $tokenReader, Session $session)
    {
        $this->connection = $connection;
        $this->tokenReader = $tokenReader;
        $this->session = $session;
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

        $userId = $this->getUserIdForToken($token);
        if (!validateSystemid($userId)) {
            return false;
        }

        $user = Objectfactory::getInstance()->getObject($userId);
        if (!$user instanceof UserUser) {
            return false;
        }

        $this->session->loginUserForRequest($user);

        return true;
    }

    private function getUserIdForToken(string $token): ?string
    {
        if (empty($token)) {
            return null;
        }

        // decode and validate JWT
        $data = JWT::decode($token, $this->tokenReader->getToken(), [self::JWT_ALG]);

        // check whether uid is set
        if (!isset($data->uid)) {
            return null;
        }

        $row = $this->connection->getPRow("SELECT user_id FROM agp_user WHERE user_accesstoken = ?", [$token]);

        if (empty($row)) {
            // access token does not exist
            return null;
        }

        if ($data->uid !== $row["user_id"]) {
            // JWT belongs to a different user
            return null;
        }

        return $row["user_id"] ?? null;
    }
}
