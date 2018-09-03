<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\System\Validators;

use Kajona\System\System\ValidatorInterface;

/**
 * Validates an hash map array structure
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 * @package module_system
 */
class HashMapValidator implements ValidatorInterface
{
    /**
     * Validates a given hash map. A hash map must be an associative array where the key is a hash and the value is
     * scalar
     *
     * @param mixed $objValue
     * @return bool
     */
    public function validate($objValue)
    {
        if (is_array($objValue)) {
            foreach ($objValue as $key => $value) {
                if (!is_scalar($value)) {
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }
}
