<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Lang;
use Kajona\System\System\Validators\DateIntervalValidator;

/**
 * Provides a simple input to enter a date interval i.e. 1 month
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 * @package module_formgenerator
 */
class FormentryDateInterval extends FormentryBase implements FormentryPrintableInterface
{
    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        //set the default validator
        $this->setObjValidator(new DateIntervalValidator());
    }

    /**
     * Renders the field itself.
     * In most cases, based on the current toolkit.
     *
     * @return string
     */
    public function renderField()
    {
        $objToolkit = Carrier::getInstance()->getObjToolkit("admin");
        $strReturn = "";
        if ($this->getStrHint() != null) {
            $strReturn .= $objToolkit->formTextRow($this->getStrHint());
        }

        $strValue = $this->getStrValue();
        $objInterval = null;
        if ($strValue instanceof \DateInterval) {
            $objInterval = $strValue;
        } elseif ($strValue != "") {
            $objInterval = new \DateInterval($strValue);
        }

        $strReturn .= $objToolkit->formInputInterval($this->getStrEntryName(), $this->getStrLabel(), $objInterval, "", $this->getBitReadonly());

        return $strReturn;
    }

    /**
     * Queries the params-array or the source-object for the mapped value.
     * If found in the params-array, the value will be used, otherwise
     * the source-objects' getter is invoked.
     */
    protected function updateValue()
    {
        $arrParams = Carrier::getAllParams();
        $strName = $this->getStrEntryName();

        $strUnit = isset($arrParams[$strName . "_unit"]) ? $arrParams[$strName . "_unit"] : null;
        $strValue = isset($arrParams[$strName]) ? (int) $arrParams[$strName] : 0;

        if (in_array($strUnit, ["D", "W", "M", "Y"]) && $strValue > 0) {
            $strDuration = "P" . $strValue . $strUnit;
            $this->setStrValue($strDuration);
        } else {
            $this->setStrValue($this->getValueFromObject());
        }
    }

    /**
     * @inheritdoc
     */
    public function getValueAsText()
    {
        $strValue = $this->getStrValue();
        $objInterval = null;
        if (!empty($strValue) && is_string($strValue)) {
            $objInterval = new \DateInterval($strValue);
        } elseif ($strValue instanceof \DateInterval) {
            $objInterval = $strValue;
        }

        if ($objInterval !== null) {
            $objLang = Lang::getInstance();
            $arrFormat = [];
            if ($objInterval->y > 0) {
                $arrFormat[] = "%y " . $objLang->getLang(($objInterval->y > 1 ? "interval_years" : "interval_year"), "elements");
            }
            if ($objInterval->m > 0) {
                $arrFormat[] = "%m " . $objLang->getLang(($objInterval->m > 1 ? "interval_months" : "interval_month"), "elements");
            }
            if ($objInterval->d > 0) {
                $arrFormat[] = "%d " . $objLang->getLang(($objInterval->d > 1 ? "interval_days" : "interval_day"), "elements");
            }
            if (count($arrFormat) > 0) {
                return $objInterval->format(implode(", ", $arrFormat));
            }
        }

        return "-";
    }
}
