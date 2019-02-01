<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/
declare(strict_types=1);

namespace Kajona\System\Admin\Formentries;

use Kajona\System\System\Carrier;
use Kajona\System\System\Reflection;

/**
 * I18n implementation of a wysiwyg-input
 *
 * @author  stefan.idler@artemeon.de
 * @since   7.1
 */
class FormentryWysiwygI18n extends AbstractFormentryI18n
{
    private $strOpener = "";
    protected $strToolbarset = "standard";

    /**
     * @param string $strFormName
     * @param string $strSourceProperty
     * @param mixed $objObject
     * @throws \Kajona\System\System\Exception
     */
    protected function buildFormEntries($strFormName, $strSourceProperty, $objObject)
    {
        if ($objObject != null && $strSourceProperty != "") {
            $objReflection = new Reflection($objObject);

            //try to find the matching source property
            $strSourceProperty = $this->getCurrentProperty(FormentryWysiwyg::STR_CONFIG_ANNOTATION);
            if ($strSourceProperty != null) {
                $this->strToolbarset = $objReflection->getAnnotationValueForProperty($strSourceProperty, FormentryWysiwyg::STR_CONFIG_ANNOTATION);
            }
        }

        foreach ($this->getPossibleI18nLanguages() as $lang) {
            $entry = new FormentryWysiwyg($strFormName, "{$strSourceProperty}_{$lang}", $objObject);
            $entry->setStrOpener($this->strOpener);
            $entry->setStrLabel($this->getStrLabel()." ({$lang})");
            $entry->setStrHint($this->getStrHint());
            $entry->setBitMandatory($this->getBitMandatory());
            $entry->setStrToolbarset($this->getStrToolbarset());
            $this->arrEntries[$lang] = $entry;
        }
    }

    /**
     * @inheritDoc
     */
    protected function updateValue()
    {
        $arrParams = Carrier::getAllParams();
        if (isset($arrParams[$this->getStrEntryName()])) {
            $this->setStrValue($this->toI18nValueString($arrParams, $this->getStrEntryName(), function ($val) {
                return processWysiwygHtmlContent($val);
            }));
        } else {
            parent::updateValue();
        }
    }


    /**
     * @param string $strOpener
     * @return FormentryTextI18n
     */
    public function setStrOpener(string $strOpener): FormentryBase
    {
        /** @var FormentryWysiwyg $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setStrOpener($strOpener);
        }
        $this->strOpener = $strOpener;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrOpener(): string
    {
        return $this->strOpener;
    }

    /**
     * @return string
     */
    public function getStrToolbarset()
    {
        return $this->strToolbarset;
    }

    /**
     * @param string $strToolbarset
     * @return $this
     */
    public function setStrToolbarset($strToolbarset)
    {
        /** @var FormentryWysiwyg $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setStrToolbarset($strToolbarset);
        }
        $this->strToolbarset = $strToolbarset;
        return $this;
    }
}
