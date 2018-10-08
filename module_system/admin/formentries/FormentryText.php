<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryI18nTrait;
use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Validators\TextValidator;

/**
 * @author  sidler@mulchprod.de
 * @since   4.0
 * @package module_formgenerator
 */
class FormentryText extends FormentryBase implements FormentryPrintableInterface
{
    use FormentryI18nTrait;

    private $strOpener = "";


    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        //set the default validator
        $this->setObjValidator(new TextValidator());
    }

    /**
     * Renders the field itself.
     * In most cases, based on the current toolkit.x
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

        if ($this->isI18nEnabled()) {
            foreach ($this->toValueArray($this->getStrValue()) as $lang => $value) {
                $strReturn .= $objToolkit->formInputText($this->getStrEntryName()."_".$lang, $this->getStrLabel()." ({$lang})", $value, "inputText", $this->strOpener, $this->getBitReadonly());
            }
            $strReturn .= $objToolkit->formInputHidden($this->getStrEntryName(), "1");
        } else {
            $strReturn .= $objToolkit->formInputText($this->getStrEntryName(), $this->getStrLabel(), $this->getStrValue(), "inputText", $this->strOpener, $this->getBitReadonly());
        }


        return $strReturn;
    }

    /**
     * @inheritDoc
     */
    protected function updateValue()
    {
        if ($this->isI18nEnabled()) {
            $arrParams = Carrier::getAllParams();
            if (isset($arrParams[$this->getStrEntryName()])) {
                $this->setStrValue($this->toValueString($arrParams, $this->getStrEntryName()));
            } else {
                parent::updateValue();
            }
        } else {
            parent::updateValue();
        }
    }

    /**
     * Uses the current validator and validates the current value.
     *
     * @return bool
     */
    public function validateValue()
    {
        return $this->getObjValidator()->validate($this->getStrValue());
    }


    /**
     * Returns a textual representation of the formentries' value.
     * May contain html, but should be stripped down to text-only.
     *
     * @return string
     */
    public function getValueAsText()
    {
        return $this->getStrValue();
    }

    /**
     * @param string $strOpener
     * @return FormentryText
     */
    public function setStrOpener($strOpener)
    {
        $this->strOpener = $strOpener;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrOpener()
    {
        return $this->strOpener;
    }
}
