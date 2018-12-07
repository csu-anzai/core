<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\Tinyurl\System;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @see \Kajona\Tinyurl\System\TinyUrl
     */
    const STR_TINY_URL_MANAGER = "tinyurl_manager";

    public function register(Container $objContainer)
    {
        $objContainer[self::STR_TINY_URL_MANAGER] = function ($c) {
            return new TinyUrl();
        };
    }
}
