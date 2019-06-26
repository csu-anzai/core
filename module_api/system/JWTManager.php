<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System;

use Firebase\JWT\JWT;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\UserUser;

/**
 * JWTManager
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class JWTManager
{
    const ALG = 'HS256';

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
     * @param UserUser $user
     * @return string
     */
    public function generate(UserUser $user): string
    {
        $exp = time() + (int) SystemSetting::getConfigValue("_system_release_time_");

        $payload = [
            "iss" => _webpath_,
            "sub" => $user->getSystemid(),
            "exp" => $exp,
            "iat" => time(),
            "name" => $user->getStrUsername(),
            "lastname" => $user->getStrName(),
            "forename" => $user->getStrForename(),
            "lang" => $user->getStrAdminlanguage(),
            "admin" => $user->getIntAdmin(),
        ];

        return JWT::encode($payload, $this->projectSecret->getToken(), self::ALG);
    }

    /**
     * @param string $token
     * @param string $userId
     * @return bool
     */
    public function validate(string $token, string $userId)
    {
        $data = JWT::decode($token, $this->projectSecret->getToken(), [self::ALG]);

        if (!isset($data->sub) || $data->sub !== $userId) {
            // JWT belongs to a different user
            return false;
        }

        if (!isset($data->iss) || $data->iss !== _webpath_) {
            // JWT was issued from another AGP instance
            return false;
        }

        return true;
    }
}
