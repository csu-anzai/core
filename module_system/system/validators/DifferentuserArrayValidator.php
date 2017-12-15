<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\System\Validators;

use Kajona\System\System\Carrier;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\UserUser;


/**
 * Validates an array of user-ids to be 1. valid and 2. to keep at least a single user different from the current logged in one
 *
 * @author sidler@mulchprod.de
 * @since 6.5
 */
class DifferentuserArrayValidator extends UserValidator
{

    /**
     * Validates the passed chunk of data.
     * In most cases, this'll be a string-object.
     *
     * @param string $objValue
     *
     * @return bool
     */
    public function validate($objValue)
    {

        $objValue = array_filter($objValue, function($strSystemid) {
            return validateSystemid($strSystemid) && Objectfactory::getInstance()->getObject($strSystemid) instanceof UserUser;
        });

        foreach ($objValue as $strOneId) {
            if ($strOneId != Carrier::getInstance()->getObjSession()->getUserID()) {
                return true;
            }
        }

        return false;


    }

}
