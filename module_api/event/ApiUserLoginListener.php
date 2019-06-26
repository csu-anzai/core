<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\Event;

use Kajona\Api\System\JWTManager;
use Kajona\Api\System\ServiceProvider;
use Kajona\System\System\Carrier;
use Kajona\System\System\CoreEventdispatcher;
use Kajona\System\System\GenericeventListenerInterface;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\SystemEventidentifier;
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

        /** @var JWTManager $jwtManager */
        $jwtManager = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_JWT_MANAGER);

        $user = Objectfactory::getInstance()->getObject($strUserid);

        if ($user instanceof UserUser && $user->getIntRecordStatus() == 1) {
            // every time the user executes a login we generate a new access token
            $token = $jwtManager->generate($user);

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
