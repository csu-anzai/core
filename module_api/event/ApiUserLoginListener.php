<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\Event;

use Firebase\JWT\JWT;
use Kajona\Api\System\Authorization\UserToken;
use Kajona\Api\System\ServiceProvider;
use Kajona\Api\System\TokenReader;
use Kajona\System\System\Carrier;
use Kajona\System\System\CoreEventdispatcher;
use Kajona\System\System\GenericeventListenerInterface;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\SystemEventidentifier;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\UserUser;

/**
 * @package module_api
 * @author christoph.kappestein@artemeon.de
 */
class ApiUserLoginListener implements GenericeventListenerInterface
{
    /**
     * @param string $strEventName
     * @param array $arrArguments
     * @return bool
     */
    public function handleEvent($strEventName, array $arrArguments)
    {
        list($strUserid) = $arrArguments;

        /** @var TokenReader $tokenReader */
        $tokenReader = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_TOKEN_READER);

        $user = Objectfactory::getInstance()->getObject($strUserid);

        if ($user instanceof UserUser && $user->getIntRecordStatus() == 1) {
            // every time the user executes a login we generate a new access token which expires also after the session
            // release time
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

            $token = JWT::encode($payload, $tokenReader->getToken(), UserToken::JWT_ALG);

            $user->setStrAccessToken($token);
            ServiceLifeCycleFactory::getLifeCycle(get_class($user))->update($user);
        }

        return true;
    }

    /**
     * @return void
     */
    public static function staticConstruct()
    {
        CoreEventdispatcher::getInstance()->removeAndAddListener(SystemEventidentifier::EVENT_SYSTEM_USERLOGIN, new ApiUserLoginListener());
    }
}

ApiUserLoginListener::staticConstruct();
