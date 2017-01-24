<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\StringUtil;
use Kajona\System\System\Validators\NumericValidator;


/**
 * A simple form-element for floats, makes use of localized decimal-separators
 *
 * @author sidler@mulchprod.de
 * @since 4.0
 * @package module_formgenerator
 */
class FormentryFloat extends FormentryBase implements FormentryPrintableInterface
{


    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        //set the default validator
        $this->setObjValidator(new NumericValidator());
    }

    public function setStrValue($strValue)
    {
        parent::setStrValue($strValue);

        //check if value comes from ui by checking if param exist. If param exists try to convert the value to a raw value
        if(Carrier::getInstance()->issetParam($this->getStrEntryName())) {
            parent::setStrValue($this->getRawValue());
        }
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

        $strValue = $this->getStrUIValue();
        $strReturn .= $objToolkit->formInputText($this->getStrEntryName(), $this->getStrLabel(), $strValue, "inputText", "", $this->getBitReadonly());

        return $strReturn;
    }

    /**
     * Returns a textual representation of the formentries' value.
     * May contain html, but should be stripped down to text-only.
     *
     * @return string
     */
    public function getValueAsText()
    {
        return $this->getStrUIValue();
    }

    /**
     * Converts the value of the formentry to a float representation (raw value)
     *
     * @return float|null
     */
    public function getRawValue()
    {
        $strFieldValue = $this->getStrValue();

        $strSyleThousand = Carrier::getInstance()->getObjLang()->getLang("numberStyleThousands", "system");
        $strStyleDecimal = Carrier::getInstance()->getObjLang()->getLang("numberStyleDecimal", "system");

        $strValue = StringUtil::replace($strSyleThousand, "", $strFieldValue);//remove first thousand separator
        $strValue = StringUtil::replace(array(",", $strStyleDecimal), ".", $strValue);//replace decimal with decimal point for db

        //in case given string is not numeric or an empty string just return that value
        if (!is_numeric($strValue) || $strValue === "") {
            return $strFieldValue;
        }

        return (float)$strValue;

    }

    /**
     * Converts the value of the formentry to UI representation
     *
     * @return mixed
     */
    public function getStrUIValue()
    {
        $strValue = $this->getStrValue();

        if (!is_numeric($strValue)) {
            return $strValue;
        }

        return numberFormat($strValue, 2);
    }
}
