<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Oauth2\System;

use GuzzleHttp\Client;
use Kajona\System\System\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @see ProviderManager
     */
    const STR_PROVIDER_MANAGER = "oauth2_provider_manager";

    /**
     * @see Client
     */
    const STR_HTTP_CLIENT = "http_client";

    public function register(Container $objContainer)
    {
        $objContainer[self::STR_PROVIDER_MANAGER] = function ($c) {
            return new ProviderManager(
                $c[self::STR_HTTP_CLIENT],
                $c[\Kajona\System\System\ServiceProvider::STR_LIFE_CYCLE_FACTORY],
                $c[\Kajona\System\System\ServiceProvider::STR_SESSION],
                Config::getInstance("module_oauth2")->getConfig("providers")
            );
        };

        $objContainer[self::STR_HTTP_CLIENT] = function ($c) {
            $httpsVerify = Config::getInstance("module_oauth2")->getConfig("https_verify");

            return new Client([
                'verify' => $httpsVerify
            ]);
        };
    }
}
