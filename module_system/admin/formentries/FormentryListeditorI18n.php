<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;

/**
 * Formentry which can be used to edit multiple text options. The formentry generates for each option a unique id which
 * is stable across the edit process
 *
 * @author stefan.idler@artemeon.de
 * @since 7.1
 * @package module_formgenerator
 */
class FormentryListeditorI18n extends AbstractFormentryI18n
{

    /**
     * @param string $strFormName
     * @param string $strSourceProperty
     * @param mixed $objObject
     * @throws \Kajona\System\System\Exception
     */
    protected function buildFormEntries($strFormName, $strSourceProperty, $objObject)
    {
        foreach ($this->getPossibleI18nLanguages() as $lang) {
            $entry = new FormentryListeditor($strFormName, "{$strSourceProperty}_{$lang}", $objObject);
            $entry->setStrLabel($this->getStrLabel()." ({$lang})");
            $entry->setStrHint($this->getStrHint());
            $entry->setBitMandatory($this->getBitMandatory());
            $entry->setBitHideLongHints($this->getBitHideLongHints());
            $this->arrEntries[$lang] = $entry;
        }
    }

}
