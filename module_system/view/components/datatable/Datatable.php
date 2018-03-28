<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Datatable;

use Kajona\System\View\Components\AbstractComponent;

/**
 * Returns a table filled with infos.
 * The header may be build using cssclass -> value or index -> value arrays
 * Values may be build using cssclass -> value or index -> value arrays, too (per row)
 * For header, the passing of the fake-classes colspan-2 and colspan-3 are allowed in order to combine cells
 * 
 * @author sidler@mulchprod.de
 * @since 7.0
 * @componentTemplate datatable/template.twig
 */
class Datatable extends AbstractComponent
{
    /**
     * The first row to name the columns
     *
     * @var array
     */
    private $arrHeaders;

    /**
     * Every entry is one row
     *
     * @var array
     */
    private $arrRows;

    /**
     * @var string
     */
    private $strTableCssAddon;

    /**
     * @var bool
     */
    private $bitWithTbody = false;

    /**
     * @var bool
     */
    private $bitWithFloatThread = false;

    /**
     * @param array $arrHeaders
     * @param array $arrRows
     */
    public function __construct(array $arrHeaders, array $arrRows)
    {
        parent::__construct();

        $this->arrHeaders = $arrHeaders;
        $this->arrRows = $arrRows;
    }

    /**
     * @param string $strTableCssAddon
     */
    public function setTableCssAddon(string $strTableCssAddon)
    {
        $this->strTableCssAddon = $strTableCssAddon;
    }

    /**
     * @param bool $bitWithTbody
     */
    public function setWithTbody(bool $bitWithTbody)
    {
        $this->bitWithTbody = $bitWithTbody;
    }

    /**
     * @param bool $bitWithFloatThread
     */
    public function setWithFloatThread(bool $bitWithFloatThread)
    {
        $this->bitWithFloatThread = $bitWithFloatThread;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        return $this->renderTemplate([
            'headers' => $this->arrHeaders,
            'rows' => $this->arrRows,
            'withTbody' => $this->bitWithTbody,
            'withFloatThread' => $this->bitWithFloatThread,
            'tableCssAddon' => $this->strTableCssAddon,
        ]);
    }
}
