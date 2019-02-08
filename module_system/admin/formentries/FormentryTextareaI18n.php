<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\Admin\Formentries;


/**
 * @author sidler@mulchprod.de
 * @since 7.1
 */
class FormentryTextareaI18n extends AbstractFormentryI18n
{

    private $strOpener = "";
    private $bitLarge = false;
    private $intNumberOfRows = 4;
    private $strPlaceholder;



    /**
     * @param string $strFormName
     * @param string $strSourceProperty
     * @param mixed $objObject
     * @throws \Kajona\System\System\Exception
     */
    protected function buildFormEntries($strFormName, $strSourceProperty, $objObject)
    {
        foreach ($this->getPossibleI18nLanguages() as $lang) {
            $entry = new FormentryTextarea($strFormName, "{$strSourceProperty}_{$lang}");
            $entry->setObjSourceObject($objObject);
            $entry->setStrOpener($this->strOpener);
            $entry->setStrLabel($this->getStrLabel()." ({$lang})");
            $entry->setStrHint($this->getStrHint());
            $entry->setBitMandatory($this->getBitMandatory());
            $entry->setBitHideLongHints($this->getBitHideLongHints());
            $entry->setBitLarge($this->getBitLarge());
            $entry->setIntNumberOfRows($this->getIntNumberOfRows());
            $entry->setStrPlaceholder($this->getStrPlaceholder());
            $this->arrEntries[$lang] = $entry;
        }
    }


    /**
     * @param $strOpener
     * @return FormentryTextareaI18n
     */
    public function setStrOpener($strOpener)
    {
        /** @var FormentryTextarea $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setStrOpener($strOpener);
        }
        $this->strOpener = $strOpener;
        return $this;
    }

    public function getStrOpener()
    {
        return $this->strOpener;
    }

    /**
     * @param boolean $bitLarge
     *
     * @return $this
     */
    public function setBitLarge($bitLarge)
    {
        /** @var FormentryTextarea $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setBitLarge($bitLarge);
        }
        $this->bitLarge = $bitLarge;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getBitLarge()
    {
        return $this->bitLarge;
    }

    /**
     * @param int $intNumberOfRows
     * @return FormentryTextareaI18n
     */
    public function setIntNumberOfRows($intNumberOfRows)
    {
        /** @var FormentryTextarea $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setIntNumberOfRows($intNumberOfRows);
        }
        $this->intNumberOfRows = $intNumberOfRows;
        return $this;
    }

    /**
     * @return int
     */
    public function getIntNumberOfRows()
    {
        return $this->intNumberOfRows;
    }

    /**
     * @return string
     */
    public function getStrPlaceholder()
    {
        return $this->strPlaceholder;
    }

    /**
     * @param string $strPlaceholder
     * @return FormentryTextareaI18n
     */
    public function setStrPlaceholder($strPlaceholder)
    {
        /** @var FormentryTextarea $objEntry */
        foreach ($this->arrEntries as $lang => $objEntry) {
            $objEntry->setStrPlaceholder($strPlaceholder);
        }
        $this->strPlaceholder = $strPlaceholder;
        return $this;
    }
}
