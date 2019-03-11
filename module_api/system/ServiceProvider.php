<?php

namespace Kajona\Api\System;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @see AppBuilder
     */
    const STR_APP_BUILDER = "api_app_builder";

    /**
     * @see EndpointScanner
     */
    const STR_ENDPOINT_SCANNER = "api_endpoint_scanner";

    public function register(Container $objContainer)
    {
        $objContainer[self::STR_APP_BUILDER] = function ($c) {
            return new AppBuilder(
                $c[self::STR_ENDPOINT_SCANNER],
                $c
            );
        };

        $objContainer[self::STR_ENDPOINT_SCANNER] = function ($c) {
            return new EndpointScanner(
                $c[\Kajona\System\System\ServiceProvider::STR_CACHE_MANAGER]
            );
        };
    }
}
