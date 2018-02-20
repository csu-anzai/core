<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Datatable;

use Kajona\System\System\StringUtil;
use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a table filled with infos.
 * The header may be build using cssclass -> value or index -> value arrays
 * Values may be build using cssclass -> value or index -> value arrays, too (per row)
 * For header, the passing of the fake-classes colspan-2 and colspan-3 are allowed in order to combine cells
 * 
 * @author sidler@mulchprod.de
 * @since 7.0
 * @componentTemplate datatable/template.tpl
 */
class Datatable extends AbstractComponent
{
    

    /**
     * @var array $arrHeader the first row to name the columns
     */
    private $arrHeader;

    /**
     * @var array $arrValues every entry is one row
     */
    private $arrRows;

    private $strTableCssAddon = "";
    private $bitWithTbody = false;

    /**
     * Datatable constructor.
     * @param $arrHeader
     * @param $arrRows
     */
    public function __construct($arrHeader, $arrRows)
    {
        $this->arrHeader = $arrHeader;
        $this->arrRows = $arrRows;
        
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getStrTableCssAddon(): string
    {
        return $this->strTableCssAddon;
    }

    /**
     * @param string $strTableCssAddon
     */
    public function setStrTableCssAddon(string $strTableCssAddon)
    {
        $this->strTableCssAddon = $strTableCssAddon;
    }

    /**
     * @return bool
     */
    public function isBitWithTbody(): bool
    {
        return $this->bitWithTbody;
    }

    /**
     * @param bool $bitWithTbody
     */
    public function setBitWithTbody(bool $bitWithTbody)
    {
        $this->bitWithTbody = $bitWithTbody;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {

        $strReturn = "";
        //The Table header & the templates
        $strReturn .= $this->renderTemplate(array("cssaddon" => $this->strTableCssAddon), "datalist_header".($this->bitWithTbody ? "_tbody" : ""));

        //Iterating over the rows

        //Starting with the header, column by column
        if (is_array($this->arrHeader) && !empty($this->arrHeader)) {
            $strReturn .= $this->renderTemplate(array(), "datalist_column_head_header");

            $bitNrToSkip = 0;
            foreach ($this->arrHeader as $strCssClass => $strHeader) {
                $bitSkipPrint = 0;
                $strAddon = "";
                if (StringUtil::indexOf($strCssClass, "colspan-2") !== false) {
                    $strAddon = " colspan='2' ";
                    $bitSkipPrint = 1;
                    $strCssClass = StringUtil::replace("colspan-2", "", $strCssClass);
                } elseif (StringUtil::indexOf($strCssClass, "colspan-3") !== false) {
                    $strAddon = " colspan='3' ";
                    $bitSkipPrint = 2;
                    $strCssClass = StringUtil::replace("colspan-3", "", $strCssClass);
                }

                if ($bitNrToSkip-- <= 0) {
                    $strReturn .= $this->renderTemplate(array("value" => $strHeader, "class" => $strCssClass, "addons" => $strAddon), "datalist_column_head");
                }

                if ($bitSkipPrint > 0) {
                    $bitNrToSkip = $bitSkipPrint;
                }

            }

            $strReturn .= $this->renderTemplate(array(), "datalist_column_head_footer");
        }

        //And the content, row by row, column by column
        foreach ($this->arrRows as $strKey => $arrValueRow) {
            $strReturn .= $this->renderTemplate(array("systemid" => $strKey), "datalist_column_header".($this->bitWithTbody ? "_tbody" : ""));

            foreach ($arrValueRow as $strCssClass => $strValue) {
                $strReturn .= $this->renderTemplate(array("value" => $strValue, "class" => $strCssClass), "datalist_column");
            }

            $strReturn .= $this->renderTemplate(array(), "datalist_column_footer".($this->bitWithTbody ? "_tbody" : ""));
        }

        //And the footer
        $strReturn .= $this->renderTemplate(array(), "datalist_footer");
        return $strReturn;
    }

}