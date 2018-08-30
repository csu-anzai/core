<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Exception;
use Kajona\System\System\Reflection;
use Kajona\System\System\Validators\HashMapValidator;
use Kajona\System\View\Components\Formentry\Listeditor\Listeditor;

/**
 * Formentry which can be used to edit multiple text options. The formentry generates for each option a unique id which
 * is stable across the edit process
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.0
 * @package module_formgenerator
 */
class FormentryListeditor extends FormentryBase implements FormentryPrintableInterface
{
    /**
     * @inheritdoc
     */
    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        $this->setObjValidator(new HashMapValidator());
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

        $value = $this->getStrValue();
        $listEditor = new Listeditor($this->getStrEntryName(), $this->getStrLabel(), is_array($value) ? $value : []);

        $strReturn .= $listEditor->renderComponent();
        return $strReturn;
    }

    /**
     * @inheritdoc
     */
    protected function getValueFromObject()
    {
        $value = parent::getValueFromObject();

        if (!empty($value) && is_string($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function setValueToObject()
    {
        $objSourceObject = $this->getObjSourceObject();
        if ($objSourceObject == null) {
            return "";
        }

        $objReflection = new Reflection($objSourceObject);
        $strSetter = $objReflection->getSetter($this->getStrSourceProperty());
        if ($strSetter === null) {
            throw new Exception("unable to find setter for value-property ".$this->getStrSourceProperty()."@".get_class($objSourceObject), Exception::$level_ERROR);
        }

        return $objSourceObject->{$strSetter}(json_encode((object) $this->getStrValue()));
    }

    /**
     * @inheritdoc
     */
    public function getValueAsText()
    {
        $value = $this->getStrValue();

        return is_array($value) ? implode(", ", $value) : "-";
    }
}
