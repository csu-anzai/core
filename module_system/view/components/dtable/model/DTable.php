<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\Dtable\Model;

/**
 * DTable class.
 * Specifies a data table for using in DTableComponent class objects.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 */
class DTable
{
    /**
     * @var DRow[] $headers
     */
    private $headers = [];

    /**
     * @var DRow[] $rows
     */
    private $rows = [];

    /**
     * DTable constructor.
     * @param DRow[]|array $headers
     * @param DRow[]|array $rows
     */
    public function __construct(array $headers=[], array $rows=[])
    {
        if (!empty($headers)) $this->setHeaders($headers);
        if (!empty($rows)) $this->setRows($rows);
    }

    /**
     * @param array $rows
     *
     * @return DTable
     */
    public function setRows(array $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * @return DRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Adds list of new header rows into list of headers.
     *
     * @param array $headers
     *
     * @return DTable
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }

        return $this;
    }

    /**
     * @return DRow[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param DRow|array $row
     *
     * @return DTable
     */
    function addRow($row)
    {
        $this->rows[] = (!is_object($row)) ? new DRow($row) : $row;

        return $this;
    }

    /**
     * Adds new header row into list of headers.
     *
     * @param DRow|array $header
     *
     * @return DTable
     */
    function addHeader($header)
    {
        $this->headers[] = (!is_object($header)) ? new DRow($header) : $header;

        return $this;
    }

    /**
     * Return table data as a string.
     *
     * @return string
     */
    function getStringView(string $separator = " ")
    {
        $strReturn = "";

        foreach ($this->getHeaders() as $row) {
            $arrCellValues = [];
            foreach ($row->getCells() as $cell) {
                $arrCellValues[] = $cell->getValue();
            }
            $strReturn .= implode($separator, $arrCellValues);
            $strReturn .= ' <br />';
        }

        foreach ($this->getRows() as $row) {
            $arrCellValues = [];
            foreach ($row->getCells() as $cell) {
                $arrCellValues[] = $cell->getValue();
            }
            $strReturn .= implode($separator, $arrCellValues);
            $strReturn .= ' <br />';
        }

        return $strReturn;
    }

}