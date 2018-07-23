<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\DTable\Model;

/**
 * DRow class.
 * Specifies a row for using in DTable class objects.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 */
class DRow
{
    /**
     * @var array
     */
    private $cells = [];

    /**
     * @var string
     */
    private $classAddon = '';
    /**
     * DRow constructor.
     * @param array $cells
     */
    public function __construct(array $cells = []) {
        $this->setCells($cells);
    }

    /**
     * @param array $cells
     *
     * @return DRow
     */
    public function setCells(array $cells)
    {
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * @param DCell|string $cell
     *
     * @return DRow
     */
    public function addCell($cell)
    {
        $this->cells[] = (!is_object($cell)) ? new DCell($cell) : $cell;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassAddon(): string
    {
        return $this->classAddon;
    }

    /**
     * @param string $classAddon
     *
     * @return DRow
     */
    public function setClassAddon(string $classAddon)
    {
        $this->classAddon = $classAddon;

        return $this;
    }


}