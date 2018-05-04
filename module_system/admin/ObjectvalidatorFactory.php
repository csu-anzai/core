<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                              *
********************************************************************************************************/

namespace Kajona\System\Admin;

use Kajona\System\System\Reflection;
use Kajona\System\System\Root;
use Kajona\System\System\Validators\ObjectvalidatorBase;

/**
 * @author christoph.kappestein@artemeon.de
 * @author stefan.meyer@artemeon.de
 * @since 7.1
 */
class ObjectvalidatorFactory
{
    /**
     * Reads the @objectValidator annotation from the object and returns the fitting validator or null in case no
     * annotation is set
     *
     * @param Root $object
     * @return ObjectvalidatorBase|null
     */
    public static function factory(Root $object)
    {
        $reflection = new Reflection($object);
        $annotation = $reflection->getAnnotationValuesFromClass(AdminFormgenerator::STR_OBJECTVALIDATOR_ANNOTATION);
        if (count($annotation) > 0) {
            $validatorClass = $annotation[0];
            if (!class_exists($validatorClass)) {
                throw new \RuntimeException("Object validator " . $validatorClass . " not existing");
            }

            /** @var ObjectvalidatorBase $validator */
            $validator = new $validatorClass();

            // check whether we have an correct instance
            if (!$validator instanceof ObjectvalidatorBase) {
                throw new \RuntimeException("Provided object validator must be an instance of HierarchyValidatorInterface");
            }

            return $validator;
        }

        return null;
    }
}
