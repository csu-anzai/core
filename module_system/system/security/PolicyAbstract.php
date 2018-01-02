<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security;

/**
 * PolicyAbstract
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
abstract class PolicyAbstract implements PolicyInterface
{
    /**
     * @inheritdoc
     */
    public function getError()
    {
        $objReflection = new \ReflectionClass($this);
        return ["user_password_validate_" . strtolower($objReflection->getShortName()), "user"];
    }
}
