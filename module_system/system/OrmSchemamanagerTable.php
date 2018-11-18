<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                        *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Data-object used by the schema-manager internally.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.6
 */
class OrmSchemamanagerTable
{

    /**
     * @var OrmSchemamanagerRow[]
     */
    private $arrRows = array();

    private $bitTxSafe = true;

    private $strName = "";

    /**
     * @param string $strName
     */
    public function __construct($strName)
    {
        $this->strName = $strName;
    }

    /**
     * @param OrmSchemamanagerRow[] $arrRows
     */
    public function setArrRows($arrRows)
    {
        $this->arrRows = $arrRows;
    }

    /**
     * @return OrmSchemamanagerRow[]
     */
    public function getArrRows()
    {
        return $this->arrRows;
    }

    public function addRow(OrmSchemamanagerRow $objRow)
    {
        $this->arrRows[] = $objRow;
    }

    /**
     * @param string $strName
     */
    public function setStrName($strName)
    {
        $this->strName = $strName;
    }

    /**
     * @return string
     */
    public function getStrName()
    {
        return $this->strName;
    }


}
