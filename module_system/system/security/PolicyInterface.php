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
 * PolicyInterface
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
interface PolicyInterface
{
    /**
     * Validates whether the provided password is valid for the user
     *
     * @param string $strPassword
     * @param UserUser|null $objUser
     * @return bool
     */
    public function validate($strPassword, UserUser $objUser = null);

    /**
     * Returns an array which gets passed to the get lang method to render a fitting error message
     *
     * @return array
     */
    public function getError();
}
