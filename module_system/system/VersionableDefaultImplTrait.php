<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\Admin\AdminFormgenerator;

/**
 * Provides a default implementation of VersionableInterface.
 * May be used to reduce duplicate code
 *
 * @author sidler@mulchprod.de
 * @since 6.5
 * @see VersionableInterface
 */
trait VersionableDefaultImplTrait
{

    /**
     * Returns a human readable name of the action stored with the changeset.
     *
     * @param string $strAction the technical actionname
     *
     * @return string the human readable name
     */
    public function getVersionActionName($strAction)
    {
        return $strAction;
    }

    /**
     * Returns a human readable name of the property-name stored with the changeset.
     *
     * @param string $strProperty the technical property-name
     *
     * @return string the human readable name
     */
    public function getVersionPropertyName($strProperty)
    {
        //check if the property provides a matching annotation
        $objReflection = new Reflection($this);
        $strKey = $objReflection->getAnnotationValueForProperty($strProperty, AdminFormgenerator::STR_LABEL_ANNOTATION);
        $strModule = $objReflection->getParamValueForPropertyAndAnnotation($strProperty, AdminFormgenerator::STR_LABEL_ANNOTATION, "module");
        if (empty($strModule)) {
            $strModule = $this->getArrModule('module');
        }

        $strPropertyLabel = Carrier::getInstance()->getObjLang()->getLang($strKey, $strModule);
        if ($strPropertyLabel == "!{$strKey}!") {
            $strKey = "form_".$this->getArrModule('module')."_".Lang::getInstance()->propertyWithoutPrefix($strProperty);
            $strPropertyLabel = Carrier::getInstance()->getObjLang()->getLang($strKey, $strModule);
        }

        if ($strPropertyLabel == "!{$strKey}!") {
            $strPropertyLabel = $strProperty;
        }


        return $strPropertyLabel;
    }

    /**
     * Renders a stored value. Allows the class to modify the value to display, e.g. to
     * replace a timestamp by a readable string.
     *
     * @param string $strProperty
     * @param string $strValue
     *
     * @return string
     */
    public function renderVersionValue($strProperty, $strValue)
    {
        //first part: a systemid
        if (validateSystemid($strValue)) {
            $objObject = Objectfactory::getInstance()->getObject($strValue);
            if ($objObject !== null) {
                return $objObject->getStrDisplayName();
            }
        }

        //maybe a date?
        if (StringUtil::indexOf($strProperty, "date", false) !== false && Date::isDateValue($strValue)) {
            return dateToString(new Date($strValue), false);
        }

        return $strValue;
    }
}
