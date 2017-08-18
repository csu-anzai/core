<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Validators\ColorValidator;

/**
 * Color picker input field
 *
 * @author sidler@mulchprod.de
 * @since 7.0
 */
class FormentryColorpicker extends FormentryBase implements FormentryPrintableInterface
{

    /**
     * @inheritdoc
     */
    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        //set the default validator
        $this->setObjValidator(new ColorValidator());
    }

    /**
     * @inheritdoc
     */
    public function renderField()
    {
        $objToolkit = Carrier::getInstance()->getObjToolkit("admin");
        $strReturn = "";
        if ($this->getStrHint() != null) {
            $strReturn .= $objToolkit->formTextRow($this->getStrHint());
        }

        $strReturn .= $objToolkit->formInputColorPicker($this->getStrEntryName(), $this->getStrLabel(), $this->getStrValue(), $this->getBitReadonly());
        return $strReturn;
    }

    /**
     * @inheritdoc
     */
    public function getValueAsText()
    {
        return $this->getStrValue();
    }
}
