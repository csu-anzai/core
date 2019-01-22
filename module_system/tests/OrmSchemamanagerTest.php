<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Carrier;
use Kajona\System\System\Database;
use Kajona\System\System\OrmSchemamanager;

class OrmSchemamanagerTest extends Testbase
{
    /**
     * @var Database
     */
    private $connection;

    /**
     * @var OrmSchemamanager
     */
    private $manager;

    protected function setUp()
    {
        parent::setUp();

        $this->connection = Carrier::getInstance()->getObjDB();
        $this->manager = new OrmSchemamanager();
    }

    protected function tearDown()
    {
        $connection = Carrier::getInstance()->getObjDB();
        $tableNames = ["agp_ormtest", "agp_testclass", "agp_testclass_rel", "agp_testclass2_rel"];

        foreach ($tableNames as $tableName) {
            if ($connection->hasTable($tableName)) {
                $connection->_pQuery("DROP TABLE " .$tableName, array());
                Carrier::getInstance()->flushCache(Carrier::INT_CACHE_TYPE_DBTABLES);
            }
        }

        parent::tearDown();
    }

    public function testCreateTable()
    {
        $this->assertTrue(!$this->connection->hasTable("agp_ormtest"));

        $this->manager->createTable(OrmSchematestTestclass::class);
        Carrier::getInstance()->flushCache(Carrier::INT_CACHE_TYPE_DBTABLES);

        $this->assertTrue($this->connection->hasTable("agp_ormtest"));

        //fetch table informations
        $columnNames = $this->connection->getTableInformation("agp_ormtest")->getColumnNames();

        $this->assertTrue(in_array("content_id", $columnNames));
        $this->assertTrue(in_array("col1", $columnNames));
        $this->assertTrue(in_array("col2", $columnNames));
        $this->assertTrue(in_array("col3", $columnNames));
    }

    /**
     * @expectedException \Kajona\System\System\OrmException
     * @expectedExceptionMessage Class Kajona\System\Tests\OrmSchematestTestclassTargettable1 provides no target-table!
     */
    public function testTargetTableException1()
    {
        $this->manager->createTable(OrmSchematestTestclassTargettable1::class);
    }

    /**
     * @expectedException \Kajona\System\System\OrmException
     * @expectedExceptionMessage Target table for Kajona\System\Tests\OrmSchematestTestclassTargettable2 is not in table.primaryColumn format
     */
    public function testTargetTableException2()
    {
        $this->manager->createTable(OrmSchematestTestclassTargettable2::class);
    }

    /**
     * @expectedException \Kajona\System\System\OrmException
     * @expectedExceptionMessage Datatype extralong is unknown (longCol3@Kajona\System\Tests\OrmSchematestTestclassDatatype)
     */
    public function testDataTypeException()
    {
        $this->manager->createTable(OrmSchematestTestclassDatatype::class);
    }

    /**
     * @expectedException \Kajona\System\System\OrmException
     * @expectedExceptionMessage Syntax for tableColumn annotation at property longCol3@Kajona\System\Tests\OrmSchematestTestclassTablecolumn not in format table.columnName
     */
    public function testTableColumnSyntaxException()
    {
        $this->manager->createTable(OrmSchematestTestclassTablecolumn::class);
    }

    public function testAssignmentTableCreation()
    {
        $this->assertTrue(!$this->connection->hasTable("agp_testclass"));
        $this->assertTrue(!$this->connection->hasTable("agp_testclass_rel"));
        $this->assertTrue(!$this->connection->hasTable("agp_testclass2_rel"));

        $this->manager->createTable(OrmSchematestTestclassAssignments::class);
        Carrier::getInstance()->flushCache(Carrier::INT_CACHE_TYPE_DBTABLES);

        $this->assertTrue($this->connection->hasTable("agp_testclass"));
        $this->assertTrue($this->connection->hasTable("agp_testclass_rel"));
        $this->assertTrue($this->connection->hasTable("agp_testclass2_rel"));

        //fetch table informations
        $columnNames = $this->connection->getTableInformation("agp_testclass_rel")->getColumnNames();

        $this->assertTrue(in_array("testclass_source_id", $columnNames));
        $this->assertTrue(in_array("testclass_target_id", $columnNames));

        $columnNames = $this->connection->getTableInformation("agp_testclass2_rel")->getColumnNames();

        $this->assertTrue(in_array("testclass_source_id", $columnNames));
        $this->assertTrue(in_array("testclass_target_id", $columnNames));
    }

    public function testUpdateTable()
    {
        $this->manager->createTable(OrmSchematestTestclass::class);
        Carrier::getInstance()->flushCache(Carrier::INT_CACHE_TYPE_DBTABLES);

        $columnNames = $this->connection->getTableInformation("agp_ormtest")->getColumnNames();
        $this->assertEquals(["content_id", "col1", "col2", "col3"], $columnNames);

        $this->manager->updateTable(OrmSchematestUpdateTestclass::class);
        Carrier::getInstance()->flushCache(Carrier::INT_CACHE_TYPE_DBTABLES);

        $columnNames = $this->connection->getTableInformation("agp_ormtest")->getColumnNames();
        $this->assertEquals(["content_id", "col1", "col2", "col3", "col4"],$columnNames);
    }
}

/**
 *
 * @targetTable agp_ormtest.content_id
 */
class OrmSchematestTestclass
{

    /**
     * @var string
     * @tableColumn agp_ormtest.col1
     */
    private $strCol1 = "";

    /**
     * @var string
     * @tableColumn agp_ormtest.col2
     * @tableColumnDatatype text
     */
    private $strCol2 = "";

    /**
     * @var int
     * @tableColumn agp_ormtest.col3
     * @tableColumnDatatype long
     */
    private $longCol3 = 0;
}

/**
 *
 * @targetTable agp_ormtest.content_id
 */
class OrmSchematestTestclassDatatype
{

    /**
     * @var int
     * @tableColumn agp_ormtest.col3
     * @tableColumnDatatype extralong
     */
    private $longCol3 = 0;
}

/**
 *
 * @targetTable agp_ormtest.content_id
 * @targetTable agp_ormtest2.content_id
 */
class OrmSchematestTestclassTablecolumn
{

    /**
     * @var int
     * @tableColumn agp_ormtestcol3
     * @tableColumnDatatype long
     */
    private $longCol3 = 0;
}


/**
 *
 */
class OrmSchematestTestclassTargettable1
{


}

/**
 * @targetTable agp_ormtest
 */
class OrmSchematestTestclassTargettable2
{


}

/**
 *
 * @targetTable agp_testclass.testclass_id
 */
class OrmSchematestTestclassAssignments
{

    /**
     * @var array
     * @objectList agp_testclass_rel (source="testclass_source_id", target="testclass_target_id")
     */
    private $arrObject1 = array();


    /**
     * @var array
     * @objectList agp_testclass2_rel (source="testclass_source_id", target="testclass_target_id")
     */
    private $arrObject2 = array();

}


/**
 *
 * @targetTable agp_ormtest.content_id
 */
class OrmSchematestUpdateTestclass
{

    /**
     * @var string
     * @tableColumn agp_ormtest.col4
     */
    private $strCol4 = "";
}
