<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Ldap\System;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider
 *
 * @author stefan.meyer@artemeon.de
 * @since 7.0
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @see \Kajona\Ldap\System\LdapAuthenticatorInterface
     */
    const STR_LDAP_AUTHENTICATOR = "ldap_authenticateusername_generator";


    public function register(Container $objContainer)
    {
        $objContainer[self::STR_LDAP_AUTHENTICATOR] = function ($c) {
            return new LdapAuthenticator();
        };
    }
}
