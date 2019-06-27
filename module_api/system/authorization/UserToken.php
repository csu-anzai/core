<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Kajona\Api\System\AuthorizationInterface;
use Kajona\Api\System\JWTManager;
use Kajona\System\System\Database;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\Session;
use Kajona\System\System\UserUser;
use Slim\Http\Request;

/**
 * Authorization service which uses the access token from a user
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class UserToken implements AuthorizationInterface
{
    /**
     * @var Database
     */
    private $connection;

    /**
     * @var JWTManager
     */
    private $jwtManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Database $connection
     * @param JWTManager $jwtManager
     * @param Session $session
     */
    public function __construct(Database $connection, JWTManager $jwtManager, Session $session)
    {
        $this->connection = $connection;
        $this->jwtManager = $jwtManager;
        $this->session = $session;
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

        $userId = $this->getUserIdForToken($token);
        if (!validateSystemid($userId)) {
            return false;
        }

        $user = Objectfactory::getInstance()->getObject($userId);
        if (!$user instanceof UserUser) {
            return false;
        }

        if ($user->getIntRecordStatus() != 1) {
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

        $row = $this->connection->getPRow("SELECT user_id FROM agp_user WHERE user_accesstoken = ?", [$token]);
        $userId = $row["user_id"] ?? null;

        if (!validateSystemid($userId)) {
            return null;
        }

        if (!$this->jwtManager->validate($token, $userId)) {
            return null;
        }

        return $userId;
    }
}
