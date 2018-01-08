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
 * Policy which checks whether a password has a specific length
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class MinLength extends PolicyAbstract
{
    /**
     * @var int
     */
    protected $intLength;

    /**
     * @param int $intLength
     */
    public function __construct(int $intLength = 8)
    {
        $this->intLength = $intLength;
    }

    /**
     * @inheritdoc
     */
    public function validate($strPassword, UserUser $objUser = null)
    {
        return StringUtil::length($strPassword) >= $this->intLength;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return ["user_password_validate_minlength", "user", [
            $this->intLength
        ]];
    }
}
