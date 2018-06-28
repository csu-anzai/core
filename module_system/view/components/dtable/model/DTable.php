<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\Datatable\Model\DTable;

use Kajona\System\View\Components\Datatable\Model\DRow\DRow;

/**
 * DTable class.
 * Specifies a data table for using in DTableComponent class objects.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 */
class DTable
{
    private $headers = [];

    private $rows = [];

    /**
     * DTable constructor.
     * @param array $headers
     * @param array $rows
     */
    public function __construct($headers=[], $rows=[])
    {
        if (!empty($headers)) $this->setHeaders($headers);
        if (!empty($rows)) $this->setRows($rows);
    }

    /**
     * @param array $rows
     */
    public function setRows(array $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    /**
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getRowsJson(): array
    {
        $jsonRows = [];
        foreach ($this->rows as $row) {
            $jsonRows[] = $row->getCellsJson();
        }
        return $jsonRows;
    }

    /**
     * @param DRow|array $row
     */
    function addRow($row)
    {
        $this->rows[] = (!is_object($row)) ? new DRow($row) : $row;
    }

    /**
     * @param DRow|array $header
     */
    function addHeader($header)
    {
        $this->headers[] = (!is_object($header)) ? new DRow($header) : $header;
    }


}