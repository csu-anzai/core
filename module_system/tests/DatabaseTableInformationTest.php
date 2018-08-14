<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Carrier;
use Kajona\System\System\Db\Schema\TableIndex;
use Kajona\System\System\Db\Schema\TableKey;
use Kajona\System\System\DbDatatypes;
use Kajona\System\System\StringUtil;

class DatabaseTableInformationTest extends Testbase
{

    const TEST_TABLE_NAME = "agp_temp_tableinfotest";

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
        $this->assertTrue($objDB->createIndex(self::TEST_TABLE_NAME, "temp_double", ["temp_double"]));
        $this->assertTrue($objDB->createIndex(self::TEST_TABLE_NAME, "temp_char500", ["temp_char500"]));
        $this->assertTrue($objDB->createIndex(self::TEST_TABLE_NAME, "temp_combined", ["temp_double", "temp_char500"]));

        //load the schema info from the db
        $info = $objDB->getTableInformation(self::TEST_TABLE_NAME);

        $arrKeyNames = array_map(function (TableKey $key) {
            return $key->getName();
        }, $info->getPrimaryKeys());

        $this->assertTrue(in_array("temp_int", $arrKeyNames));

        $arrIndexNames = array_map(function (TableIndex $index) {
            return $index->getName();
        }, $info->getIndexes());

        $this->assertTrue(in_array("temp_double", $arrIndexNames));
        $this->assertTrue(in_array("temp_char500", $arrIndexNames));
        $this->assertTrue(in_array("temp_combined", $arrIndexNames));


    }

}
