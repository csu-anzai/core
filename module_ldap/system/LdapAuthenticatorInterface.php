<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Ldap\System;

use Kajona\Ldap\System\Usersources\UsersourcesUserLdap;
use Kajona\System\System\Usersources\UsersourcesUserInterface;

/**
 * Interface for authenticating to LDAP
 *
 * @author stefan.meyer@artemeon.de
 * @module ldap
 */
interface LdapAuthenticatorInterface
{
    /**
     * Tries to authenticate a user with the given credentials.
     * The password is unencrypted, each source should take care of its own encryption.
     *
     * @param UsersourcesUserInterface|UsersourcesUserLdap $objUser
     * @param string $strPassword
     *
     * @return bool
     */
    public function authenticateUser(UsersourcesUserInterface $objUser, $strPassword);
}
