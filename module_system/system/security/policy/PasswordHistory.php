<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security\Policy;

use Kajona\System\System\Security\PolicyAbstract;
use Kajona\System\System\SystemPwHistory;
use Kajona\System\System\UserUser;

/**
 * Policy which checks whether the password was already used by the user
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class PasswordHistory extends PolicyAbstract
{
    /**
     * @var int
     */
    protected $intLength;

    /**
     * @param int $intLength
     */
    public function __construct(int $intLength = 10)
    {
        $this->intLength = $intLength;
    }

    /**
     * @inheritdoc
     */
    public function validate($strPassword, UserUser $objUser = null)
    {
        // in case we dont have a user we cant validate
        if ($objUser === null) {
            return true;
        }

        return SystemPwHistory::isPasswordInHistory($objUser, $strPassword, $this->intLength);
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return ["user_password_validate_passwordhistory", "user", [
            $this->intLength
        ]];
    }
}
