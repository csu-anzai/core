<?php
/*"******************************************************************************************************
*   (c) 2018 ARTEMEON                                                                                   *
*       Published under the GNU LGPL v2.1                                                               *
********************************************************************************************************/

namespace Kajona\System\View\Components\DTable\Model;

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
     * Adds list of new header rows into list of headers.
     *
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
     * @param DRow|array $row
     */
    function addRow($row)
    {
        $this->rows[] = (!is_object($row)) ? new DRow($row) : $row;
    }

    /**
     * Adds new header row into list of headers.
     *
     * @param DRow|array $header
     */
    function addHeader($header)
    {
        $this->headers[] = (!is_object($header)) ? new DRow($header) : $header;
    }


}