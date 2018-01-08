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
 * Policy which checks whether a password contains specific blacklisted keywords
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class Blacklist extends PolicyAbstract
{
    /**
     * @var array
     */
    protected $arrBlacklist;

    /**
     * @param array $arrBlacklist
     */
    public function __construct(array $arrBlacklist = [])
    {
        $this->arrBlacklist = $arrBlacklist;
    }

    /**
     * @inheritdoc
     */
    public function validate($strPassword, UserUser $objUser = null)
    {
        foreach ($this->arrBlacklist as $strBlacklist) {
            if (stripos($strPassword, $strBlacklist) !== false) {
                return false;
            }
        }

        return true;
    }
}
