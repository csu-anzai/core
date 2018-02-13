<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\System\Validators;

use Kajona\System\System\Carrier;
use Kajona\System\System\ValidationError;
use Kajona\System\System\ValidatorExtendedInterface;

/**
 * A simple validator to validate a string.
 * By default, the string must contain a single char, the max length is unlimited.
 *
 * @author sidler@mulchprod.de
 * @since 4.0
 * @package module_system
 */
class DateIntervalValidator implements ValidatorExtendedInterface
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
        if (!empty($objValue)) {
            try {
                new \DateInterval($objValue);

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getValidationMessages()
    {
        $objLang = Carrier::getInstance()->getObjLang();

        return [
            new ValidationError($objLang->getLang("commons_validator_dateinterval_validationmessage", "system", array()))
        ];
    }
}
