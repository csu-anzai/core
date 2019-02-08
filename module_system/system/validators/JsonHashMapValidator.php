<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\System\Validators;


/**
 * Validates an hash map array structure based on a json string
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 */
class JsonHashMapValidator extends HashMapValidator
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
        $validateMap = null;
        if (!empty($objValue) && is_string($objValue)) {
            $validateMap = json_decode($objValue, true);
        }
        if (is_array($objValue)) {
            $validateMap = $objValue;
        }

        return parent::validate($validateMap);
    }
}
