<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security;

use Kajona\System\System\Lang;
use Kajona\System\System\UserUser;

/**
 * Default password validator implementation. You can add different kind of policies to alter the behaviour
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class PasswordValidator implements PasswordValidatorInterface
{
    /**
     * @var Lang
     */
    protected $objLang;

    /**
     * @var PolicyInterface[]
     */
    protected $arrPolicies;

    /**
     * @param Lang $objLang
     * @param PolicyInterface[] $arrPolicies
     */
    public function __construct(Lang $objLang, array $arrPolicies = [])
    {
        $this->objLang = $objLang;
        $this->arrPolicies = $arrPolicies;
    }

    /**
     * @param PolicyInterface $objPolicy
     */
    public function addPolicy(PolicyInterface $objPolicy)
    {
        $this->arrPolicies[] = $objPolicy;
    }

    /**
     * @inheritdoc
     */
    public function validate($strPassword, UserUser $objUser = null)
    {
        foreach ($this->arrPolicies as $objPolicy) {
            if (!$objPolicy->validate($strPassword, $objUser)) {
                $strErrorMessage = $this->objLang->getLang(...$objPolicy->getError());
                throw new ValidationException($strErrorMessage);
            }
        }

        return true;
    }
}
