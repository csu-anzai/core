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
    const STR_APP_BUILDER = "api_app_builder";

    public function register(Container $objContainer)
    {
        $objContainer[self::STR_APP_BUILDER] = function ($c) {
            return new AppBuilder(
                $c[\Kajona\System\System\ServiceProvider::STR_CACHE_MANAGER],
                $c
            );
        };
    }
}
