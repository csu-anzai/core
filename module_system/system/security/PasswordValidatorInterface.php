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
 * PasswordValidatorInterface
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface PasswordValidatorInterface
{
    /**
     * Validates whether the provided password follows the rules
     *
     * @param string $strPassword
     * @param UserUser|null $objUser
     * @return bool
     */
    public function validate($strPassword, UserUser $objUser = null);
}
