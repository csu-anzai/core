<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Grid;

use Kajona\System\View\Components\AbstractComponent;

/**
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 * @componentTemplate template.twig
 */
class Grid extends AbstractComponent
{
    /**
     * @var array
     */
    protected $columnWidths;

    /**
     * @var array
     */
    protected $rows;

    /**
     * The column widths contains an array with the bootstrap col- class width. I.e. an array [2, 10] for a grid which
     * has a small left sidebar column. The sum of all columns must be 12.
     *
     * @param array $columnWidths
     */
    public function __construct(array $columnWidths)
    {
        parent::__construct();

        $this->columnWidths = $columnWidths;

        if (array_sum($columnWidths) != 12) {
            throw new \InvalidArgumentException("Sum of all columns must be 12");
        }
    }

    public function addRow(array $row)
    {
        if (count($row) != count($this->columnWidths)) {
            throw new \InvalidArgumentException("Row must have the same count as the column widths array");
        }

        $this->rows[] = $row;
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $rows = [];
        foreach ($this->rows as $row) {
            $data = [];
            foreach ($row as $column) {
                if ($column instanceof AbstractComponent) {
                    $data[] = $column->renderComponent();
                } elseif (is_string($column)) {
                    $data[] = $column;
                } else {
                    throw new \RuntimeException("Invalid column value must be either a string or AbstractComponent");
                }
            }

            $rows[] = $data;
        }

        $data = [
            "column_widths" => $this->columnWidths,
            "rows" => $rows,
        ];

        return $this->renderTemplate($data);
    }
}
