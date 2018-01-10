<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security;

use Kajona\System\System\UserUser;

/**
 * The password validator checks whether the password of a user follows specific rules. It gets used every time a user
 * wants to update or create a password
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface PasswordValidatorInterface
{
    /**
     * Validates whether the provided password follows the rules. MUST throw an ValidationException in case the password
     * does not met the requirements. The method returns basically only true since in an error case it MUST throw an
     * exception
     *
     * @param string $strPassword
     * @param UserUser|null $objUser
     * @return bool
     * @throws ValidationException
     */
    public function validate($strPassword, UserUser $objUser = null);
}
