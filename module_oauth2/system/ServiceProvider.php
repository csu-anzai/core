<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Oauth2\System;

use GuzzleHttp\Client;
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
        // @TODO for 7.1 we can remove this require
        require_once __DIR__ . "/../vendor/autoload.php";

        $objContainer[self::STR_PROVIDER_MANAGER] = function ($c) {
            return new ProviderManager(
                $c[self::STR_HTTP_CLIENT],
                $c[\Kajona\System\System\ServiceProvider::STR_LIFE_CYCLE_FACTORY]
            );
        };

        $objContainer[self::STR_HTTP_CLIENT] = function ($c) {
            return new Client();
        };
    }
}
