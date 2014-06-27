<?php
/*"******************************************************************************************************
*   (c) 2007-2014 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                        *
********************************************************************************************************/

/**
 * Caches rows fetched by the database layer. You may add additional rows in order to
 * have them accessible for other classes and the orm init handler.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 4.6
 */
class class_orm_rowcache extends class_orm_base {

    private static $arrInitRows = array();

    /**
     * Returns a single row from the currently cached init-rows
     *
     * @param string $strSystemid
     *
     * @return array|null
     */
    public static function getCachedInitRow($strSystemid) {
        if(isset(self::$arrInitRows[$strSystemid]))
            return self::$arrInitRows[$strSystemid];


        return null;
    }


    /**
     * Add a single row to the list of cached database-rows.
     * This avoids additional queries to init a single object afterwards.
     * On high-performance systems or large object-nets, this could reduce the amount of database-queries
     * fired drastically.
     * For best performance, include the matching row of the tables system, system_date and system_rights
     *
     * @param array $arrInitRow
     * @return void
     */
    public static function addSingleInitRow($arrInitRow) {
        self::$arrInitRows[$arrInitRow["system_id"]] = $arrInitRow;
    }

    /**
     * Add an array of rows to the list of cached database-rows.
     * This avoids additional queries to init a single object afterwards.
     * On high-performance systems or large object-nets, this could reduce the amount of database-queries
     * fired drastically.
     * For best performance, include the matching row of the tables system, system_date and system_rights
     *
     * @param array $arrInitRows
     * @return void
     */
    public static function addArrayOfInitRows($arrInitRows) {
        foreach($arrInitRows as $arrOneRow)
            self::addSingleInitRow($arrOneRow);
    }

    /**
     * @return array
     */
    public static function getArrInitRows() {
        return self::$arrInitRows;
    }

    /**
     * Resets the cached rows
     * @return void
     */
    public static function resetCache() {
        self::$arrInitRows = array();
    }




}
