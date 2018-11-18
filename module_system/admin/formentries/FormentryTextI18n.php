<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

use Kajona\System\Admin\AdminFormgeneratorContainerInterface;
use Kajona\System\Admin\FormentryI18nTrait;
use Kajona\System\Admin\FormentryPrintableInterface;
use Kajona\System\System\Carrier;
use Kajona\System\System\Validators\DummyValidator;
use Kajona\System\System\Validators\TextValidator;

/**
 * @author  stefan.idler@artemeon.de
 * @since   7.1
 */
class FormentryTextI18n extends FormentryBase implements FormentryPrintableInterface, AdminFormgeneratorContainerInterface
{
    use FormentryI18nTrait;

    private $strOpener = "";

    /**
     * Contains all child form entries
     *
     * @var array
     */
    protected $arrEntries;


    public function __construct($strFormName, $strSourceProperty, $objSourceObject = null)
    {
        parent::__construct($strFormName, $strSourceProperty, $objSourceObject);

        //set the default validator
        $this->setObjValidator(new DummyValidator());

        $this->buildFormEntries($strFormName, $strSourceProperty, $objSourceObject);
    }


    /**
     * @param string $strFormName
     * @param string $strSourceProperty
     * @param mixed $objObject
     * @throws \Kajona\System\System\Exception
     */
    private function buildFormEntries($strFormName, $strSourceProperty, $objObject)
    {
        foreach ($this->getPossibleLanguages() as $lang) {
            $entry = new FormentryText($strFormName, "{$strSourceProperty}_{$lang}", $objObject);
            $entry->setStrOpener($this->strOpener);
            $entry->setStrLabel($this->getStrLabel()." ({$lang})");
            $entry->setStrHint($this->getStrHint());
            $entry->setBitMandatory($this->getBitMandatory());
            $this->arrEntries[$lang] = $entry;
        }
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
        $strReturn .= $objToolkit->formInputHidden($this->getStrEntryName(), "1");

        if ($this->getStrHint() != null) {
            $strReturn .= $objToolkit->formTextRow($this->getStrHint());
        }

        $values = $this->toValueArray($this->getStrValue());
        /** @var FormentryText $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setStrValue($values[$lang]);
            $strReturn .= $objEntry->renderField();
        }

        return $strReturn;
    }

    /**
     * @inheritDoc
     */
    protected function updateValue()
    {
        $arrParams = Carrier::getAllParams();
        if (isset($arrParams[$this->getStrEntryName()])) {
            $this->setStrValue($this->toValueString($arrParams, $this->getStrEntryName()));
        } else {
            parent::updateValue();
        }
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


    /**
     * @inheritdoc
     */
    public function getFields(): array
    {
        return $this->arrEntries;
    }

    /**
     * @inheritdoc
     */
    public function getField($name)
    {
        return $this->arrEntries[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function hasField($name): bool
    {
        return isset($this->arrEntries[$name]);
    }

    /**
     * @inheritdoc
     */
    public function removeField($name)
    {
        if (isset($this->arrEntries[$name])) {
            unset($this->arrEntries[$name]);
        }
    }
}
