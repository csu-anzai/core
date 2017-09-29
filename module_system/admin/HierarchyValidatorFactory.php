<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\Admin;

use Kajona\System\System\HierarchyValidatorInterface;
use Kajona\System\System\Validators\HierarchyValidatorBase;
use Kajona\System\System\Exception;
use Kajona\System\System\Reflection;
use Kajona\System\System\Root;


/**
 * Form generator factory which creates HierarchyValidator instances based on a model. The hierarchy validator class
 * can be specified by a @hierarchyValidator annotation on the model
 *
 * @author stefan.meyer1@yahoo.de
 * @since  6.5
 * @module module_system
 */
class HierarchyValidatorFactory
{
    const  STR_HIERARCHYVALIDATOR_ANNOTATION = "@hierarchyValidator";

    /**
     * @param Root $objObject
     * @return HierarchyValidatorInterface|null
     * @throws Exception
     */
    public static function newHierarchyValidator(Root $objObject)
    {
        $objReflection = new Reflection($objObject);
        $arrHierarchyValidator = $objReflection->getAnnotationValuesFromClass(self::STR_HIERARCHYVALIDATOR_ANNOTATION);

        if (count($arrHierarchyValidator) > 0) {
            $strHierarchyValidator = $arrHierarchyValidator[0];
            if (!class_exists($strHierarchyValidator)) {
                throw new Exception("Hierarchy validator " . $strHierarchyValidator . " not existing", Exception::$level_ERROR);
            }

            /** @var \Kajona\System\System\Validators\HierarchyValidatorBase $objValidator */
            $objValidator = new $strHierarchyValidator();

            // check whether we have an correct instance
            if (!$objValidator instanceof HierarchyValidatorInterface) {
                throw new Exception("Provided Hierarchy validator must be an instance of HierarchyValidatorInterface", Exception::$level_ERROR);
            }

            return $objValidator;
        }

        return null;
    }
}
