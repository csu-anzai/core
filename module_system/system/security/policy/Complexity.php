<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security\Policy;

use Kajona\System\System\Security\PolicyAbstract;
use Kajona\System\System\UserUser;

/**
 * Policy which checks whether a password fulfils a specific complexity
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class Complexity extends PolicyAbstract
{
    /**
     * @var int
     */
    protected $intAlphaLower;

    /**
     * @var int
     */
    protected $intAlphaUpper;

    /**
     * @var int
     */
    protected $intDigit;

    /**
     * @var int
     */
    protected $intSpecial;

    /**
     * @param int $intAlphaLower
     * @param int $intAlphaUpper
     * @param int $intDigit
     * @param int $intSpecial
     */
    public function __construct(int $intAlphaLower = 1, int $intAlphaUpper = 1, int $intDigit = 1, int $intSpecial = 0)
    {
        $this->intAlphaLower = $intAlphaLower;
        $this->intAlphaUpper = $intAlphaUpper;
        $this->intDigit = $intDigit;
        $this->intSpecial = $intSpecial;
    }

    /**
     * @inheritdoc
     */
    public function validate($strPassword, UserUser $objUser = null)
    {
        $intLen = strlen($strPassword);
        $intAlphaUpper = $intAlphaLower = $intDigit = $intSpecial = 0;
        for ($intI = 0; $intI < $intLen; $intI++) {
            if (ctype_upper($strPassword[$intI])) { // alpha upper
                $intAlphaUpper++;
            } elseif (ctype_lower($strPassword[$intI])) { // alpha lower
                $intAlphaLower++;
            } elseif (ctype_digit($strPassword[$intI])) { // digit
                $intDigit++;
            } elseif (ctype_print($strPassword[$intI])) { // special
                $intSpecial++;
            } else {
                throw new \RuntimeException("Password contains invalid characters");
            }
        }

        if ($intAlphaUpper < $this->intAlphaUpper) {
            return false;
        }

        if ($intAlphaLower < $this->intAlphaLower) {
            return false;
        }

        if ($intDigit < $this->intDigit) {
            return false;
        }

        if ($intSpecial < $this->intSpecial) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return ["user_password_validate_complexity", "user", [
            $this->intAlphaLower,
            $this->intAlphaUpper,
            $this->intDigit,
            $this->intSpecial,
        ]];
    }
}
