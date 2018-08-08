<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Carrier;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\StringUtil;

class DatabaseColumnTypeTest extends Testbase
{

    const TEST_TABLE_NAME = "agp_temp_typetest";

    public function tearDown()
    {
        $this->flushDBCache();
        foreach (array(self::TEST_TABLE_NAME) as $strOneTable) {

            if (in_array($strOneTable, Carrier::getInstance()->getObjDB()->getTables())) {
                $strQuery = "DROP TABLE ".$strOneTable;
                Carrier::getInstance()->getObjDB()->_pQuery($strQuery, array());
            }

        }

        parent::tearDown();
    }

    public function testTypeConversion()
    {

        $objDB = Carrier::getInstance()->getObjDB();


        if (in_array(self::TEST_TABLE_NAME, Carrier::getInstance()->getObjDB()->getTables())) {
            $strQuery = "DROP TABLE ".self::TEST_TABLE_NAME;
            Carrier::getInstance()->getObjDB()->_pQuery($strQuery, array());
        }

        $colDefinitions = array();
        $colDefinitions["temp_int"] = array(DbDatatypes::STR_TYPE_INT, false);
        $colDefinitions["temp_long"] = array(DbDatatypes::STR_TYPE_LONG, true);
        $colDefinitions["temp_double"] = array(DbDatatypes::STR_TYPE_DOUBLE, true);
        $colDefinitions["temp_char10"] = array(DbDatatypes::STR_TYPE_CHAR10, true);
        $colDefinitions["temp_char20"] = array(DbDatatypes::STR_TYPE_CHAR20, true);
        $colDefinitions["temp_char100"] = array(DbDatatypes::STR_TYPE_CHAR100, true);
        $colDefinitions["temp_char254"] = array(DbDatatypes::STR_TYPE_CHAR254, true);
        $colDefinitions["temp_char500"] = array(DbDatatypes::STR_TYPE_CHAR500, true);
        $colDefinitions["temp_text"] = array(DbDatatypes::STR_TYPE_TEXT, true);
        $colDefinitions["temp_longtext"] = array(DbDatatypes::STR_TYPE_LONGTEXT, true);

        $this->assertTrue($objDB->createTable(self::TEST_TABLE_NAME, $colDefinitions, ["temp_int"]));

        //fetch all columns from the table and match the types
        $columnsFromDb = $objDB->getColumnsOfTable(self::TEST_TABLE_NAME);

        //var_dump($columnsFromDb);

        foreach ($columnsFromDb as $columnName => $details) {

            //compare both internal types converted to db-based types, those need to match
            $this->assertEquals(
                Carrier::getInstance()->getObjDB()->getDatatype(StringUtil::trim($colDefinitions[$columnName][0])),
                Carrier::getInstance()->getObjDB()->getDatatype(StringUtil::trim($details["columnType"]))
            );
        }

        $strQuery = "DROP TABLE ".self::TEST_TABLE_NAME;
        $this->assertTrue($objDB->_pQuery($strQuery, array()));

    }

}
