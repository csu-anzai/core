<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\DTable\Model\DRow;

use Kajona\System\View\Components\DTable\Model\DCell\DCell;

/**
 * DRow class.
 * Specifies a row for using in DTable class objects.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 */
class DRow
{
    private $cells = [];

    /**
     * DRow constructor.
     * @param array $cells
     */
    function __construct(array $cells = []) {
        $this->setCells($cells);
    }

    /**
     * @param array $cells
     */
    function setCells(array $cells)
    {
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }
    }

    /**
     * @return array
     */
    function getCells()
    {
        return $this->cells;
    }

    /**
     * @param DCell|string $cell
     */
    function addCell($cell)
    {
        $this->cells[] = (!is_object($cell)) ? new DCell($cell) : $cell;
    }
}