<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                    *
********************************************************************************************************/

namespace Kajona\System\System;

use AGP\Prozessverwaltung\Admin\Formentries\FormentryOe;
use AGP\Prozessverwaltung\Admin\Formentries\FormentryProzess;
use Kajona\System\Admin\AdminFormgenerator;
use Kajona\System\Admin\Formentries\FormentryDate;
use Kajona\System\Admin\Formentries\FormentryDatetime;
use Kajona\System\Admin\Formentries\FormentryDropdown;
use Kajona\System\Admin\Formentries\FormentryObjectlist;

/**
 * Class which provides a default render implementation for the VersionableInterface. The implementation looks at the
 * property and tries to find the best way to render a value depending on the available annotations
 *
 * <code>
 * SystemChangelogRenderer::renderPropertyName();
 * SystemChangelogRenderer::renderValue();
 * </code>
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @module system
 * @moduleId _system_modul_id_
 */
class SystemChangelogRenderer
{
    /**
     * @var Reflection
     */
    protected $objReflection;

    /**
     * @var Lang
     */
    protected $objLang;

    /**
     * @var string
     */
    protected $strModule;

    /**
     * @var SystemChangelogRenderer[]
     */
    private static $arrRenderer = array();

    public function __construct(Reflection $objReflection)
    {
        $this->objReflection = $objReflection;
        $this->objLang = Lang::getInstance();

        $arrModule = $objReflection->getAnnotationValuesFromClass("@module");
        $this->strModule = is_array($arrModule) ? current($arrModule) : $arrModule;
    }

    /**
     * We try to get the fitting property name through a form lang property
     *
     * @param string $strProperty
     * @return string
     */
    public function getVersionPropertyName($strProperty)
    {
        if (!in_array(substr($strProperty, 0, 3), array("str", "int", "flo", "bit", "lon"))) {
            // in this case we have probably already a translated property
            return $strProperty;
        }

        $strLabel = $this->objReflection->getAnnotationValueForProperty($strProperty, AdminFormgenerator::STR_LABEL_ANNOTATION);
        if (!empty($strLabel)) {
            $strPropertyName = $this->objLang->getLang($strLabel, $this->strModule);
            if (!empty($strPropertyName)) {
                return $strPropertyName;
            }
        }

        return $strProperty;
    }

    /**
     * Renders the value depending on the field type annotation
     *
     * @param string $strProperty
     * @param mixed $strValue
     * @return string
     */
    public function getVersionValue($strProperty, $strValue)
    {
        $strType = $this->objReflection->getAnnotationValueForProperty($strProperty, AdminFormgenerator::STR_TYPE_ANNOTATION);
        if (!empty($strType)) {
            $strDDValues = $this->objReflection->getAnnotationValueForProperty($strProperty, FormentryDropdown::STR_DDVALUES_ANNOTATION);
            if (!empty($strDDValues)) {
                $arrDDValues = FormentryDropdown::convertDDValueStringToArray($strDDValues, $this->strModule);
            } else {
                $arrDDValues = null;
            }

            return $this->renderData($strType, $strValue, $arrDDValues);
        }

        return $strValue;
    }

    /**
     * @param string $strType
     * @param string $strValue
     * @param array $arrDDValues
     * @return string
     */
    private function renderData($strType, $strValue, $arrDDValues)
    {
        switch ($strType) {
            case FormentryDate::class:
            case FormentryDatetime::class:
            case "date":
            case "datetime":
                return FormentryRenderer::renderDate($strValue);
                break;

            case FormentryDropdown::class:
            case "dropdown":
                return FormentryRenderer::renderDropdown($strValue, $arrDDValues);
                break;

            case FormentryObjectlist::class:
            case FormentryProzess::class:
            case FormentryOe::class:
            case "objectlist":
            case "prozess":
            case "oe":
                return FormentryRenderer::renderSystemIds($strValue);
                break;

            default:
                return FormentryRenderer::renderText($strValue);
        }
    }

    /**
     * @param Model $objObject
     * @param string $strProperty
     * @return string
     */
    public static function renderPropertyName(Model $objObject, $strProperty)
    {
        $strClass = get_class($objObject);
        if (!isset(self::$arrRenderer[$strClass])) {
            self::$arrRenderer[$strClass] = new self(new Reflection($strClass));
        }

        return self::$arrRenderer[$strClass]->getVersionPropertyName($strProperty);
    }

    /**
     * @param Model $objObject
     * @param string $strProperty
     * @param string $strValue
     * @return string
     */
    public static function renderValue(Model $objObject, $strProperty, $strValue)
    {
        $strClass = get_class($objObject);
        if (!isset(self::$arrRenderer[$strClass])) {
            self::$arrRenderer[$strClass] = new self(new Reflection($strClass));
        }

        return self::$arrRenderer[$strClass]->getVersionValue($strProperty, $strValue);
    }
}
