<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security\Policy;

use Kajona\System\System\Security\PolicyAbstract;
use Kajona\System\System\StringUtil;
use Kajona\System\System\UserUser;

/**
 * Policy which checks whether the password is not equals the user name and that the password does not contain two
 * characters which are also available in the user name
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class UserName extends PolicyAbstract
{
    /**
     * @inheritdoc
     */
    public function validate($strPassword, UserUser $objUser = null)
    {
        // in case we dont have a user we cant validate
        if ($objUser === null) {
            return true;
        }

        if (StringUtil::equals($objUser->getStrName(), $strPassword) === 0) {
            return false;
        }

        $intLength = StringUtil::length($strPassword);
        for ($intI = 0; $intI < $intLength; $intI++) {
            $strPair = StringUtil::substring($strPassword, $intI, 4);
            if (StringUtil::length($strPair) == 4 && StringUtil::indexOf($objUser->getStrUsername(), $strPair, false) !== false) {
                return false;
            }
        }

        return true;
    }
}
