<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Carrier;
use Kajona\System\System\Database;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Model;
use Kajona\System\System\ModelInterface;
use Kajona\System\System\OrmObjectlist;
use Kajona\System\System\OrmSchemamanager;

/**
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class OrmObjectlistTest extends Testbase
{
    protected function setUp()
    {
        $objSchema = new OrmSchemamanager();
        $objSchema->createTable(OrmObjectlistBar::class);
        $objSchema->createTable(OrmObjectlistBaz::class);

        parent::setUp();

        // insert data
        for ($i = 0; $i < 4; $i++) {
            $bar = new OrmObjectlistBar();
            $bar->setStrFoo("foo");
            $bar->setStrBar("bar");
            ServiceLifeCycleFactory::getLifeCycle(get_class($bar))->update($bar);
        }

        for ($i = 0; $i < 8; $i++) {
            $baz = new OrmObjectlistBaz();
            $baz->setStrFoo("foo");
            $baz->setStrBaz("baz");
            ServiceLifeCycleFactory::getLifeCycle(get_class($baz))->update($baz);
        }
    }

    protected function tearDown()
    {
        parent::tearDown();

        $objDb = Carrier::getInstance()->getObjDB();
        $objDb->_pQuery("DROP TABLE agp_listtest_foo", array());
        $objDb->_pQuery("DROP TABLE agp_listtest_bar", array());
        $objDb->_pQuery("DROP TABLE agp_listtest_baz", array());
        Carrier::getInstance()->flushCache(Carrier::INT_CACHE_TYPE_DBTABLES);
    }

    public function testObjectCount()
    {
        $orm = new OrmObjectlist();
        $count = $orm->getObjectCount(OrmObjectlistBar::class);

        $this->assertEquals(4, $count);

        $orm = new OrmObjectlist();
        $count = $orm->getObjectCount(OrmObjectlistBaz::class);

        $this->assertEquals(8, $count);
    }

    public function testObjectCountMulti()
    {
        $orm = new OrmObjectlist();
        $count = $orm->getObjectCount([OrmObjectlistBar::class, OrmObjectlistBaz::class]);

        $this->assertEquals(12, $count);
    }

    public function testObjectList()
    {
        $orm = new OrmObjectlist();
        $result = $orm->getObjectList(OrmObjectlistBar::class);

        $this->assertEquals(4, count($result));

        foreach ($result as $entity) {
            $this->assertInstanceOf(OrmObjectlistBar::class, $entity);
            $this->assertEquals("foo", $entity->getStrFoo());
            $this->assertEquals("bar", $entity->getStrBar());
        }

        $orm = new OrmObjectlist();
        $result = $orm->getObjectList(OrmObjectlistBaz::class);

        $this->assertEquals(8, count($result));

        foreach ($result as $entity) {
            $this->assertInstanceOf(OrmObjectlistBaz::class, $entity);
            $this->assertEquals("foo", $entity->getStrFoo());
            $this->assertEquals("baz", $entity->getStrBaz());
        }
    }

    public function testObjectListMulti()
    {
        $orm = new OrmObjectlist();
        $result = $orm->getObjectList([OrmObjectlistBar::class, OrmObjectlistBaz::class]);

        $this->assertEquals(12, count($result));

        foreach ($result as $i => $entity) {
            $this->assertInstanceOf(OrmObjectlistFoo::class, $entity);
            // we have the shared properties
            $this->assertEquals("foo", $entity->getStrFoo());

            if ($i > 3) {
                $this->assertInstanceOf(OrmObjectlistBaz::class, $entity);
                // we dont have specific properties
                $this->assertNull($entity->getStrBaz());
            } else {
                $this->assertInstanceOf(OrmObjectlistBar::class, $entity);
                // we dont have specific properties
                $this->assertNull($entity->getStrBar());
            }
        }
    }

    public function testObjectListMultiPaged()
    {
        $orm = new OrmObjectlist();
        $result = $orm->getObjectList([OrmObjectlistBar::class, OrmObjectlistBaz::class], "", 0, 5);

        $this->assertEquals(6, count($result));

        foreach ($result as $i => $entity) {
            $this->assertInstanceOf(OrmObjectlistFoo::class, $entity);
            // we have the shared properties
            $this->assertEquals("foo", $entity->getStrFoo());
        }
    }

    public function testObjectListAbstract()
    {
        $orm = new OrmObjectlist();
        $result = $orm->getObjectList(OrmObjectlistFoo::class);

        $this->assertEquals(12, count($result));

        foreach ($result as $entity) {
            $this->assertInstanceOf(OrmObjectlistFoo::class, $entity);
            $this->assertEquals("foo", $entity->getStrFoo());
        }
    }
}



/**
 * @targetTable agp_listtest_foo.foo_id
 * @module system
 * @moduleId _system_modul_id_
 */
class OrmObjectlistFoo extends Model implements ModelInterface
{
    /**
     * @var string
     * @tableColumn agp_listtest_foo.foo
     * @tableColumnDatatype char20
     */
    private $strFoo = null;

    public function getStrDisplayName()
    {
        return static::class;
    }

    /**
     * @return string
     */
    public function getStrFoo()
    {
        return $this->strFoo;
    }

    /**
     * @param string $strFoo
     */
    public function setStrFoo($strFoo)
    {
        $this->strFoo = $strFoo;
    }
}

/**
 * @targetTable agp_listtest_bar.bar_id
 * @module system
 * @moduleId _system_modul_id_
 */
class OrmObjectlistBar extends OrmObjectlistFoo
{
    /**
     * @var string
     * @tableColumn agp_listtest_bar.bar
     * @tableColumnDatatype char20
     */
    private $strBar = null;

    /**
     * @return string
     */
    public function getStrBar()
    {
        return $this->strBar;
    }

    /**
     * @param string $strBar
     */
    public function setStrBar($strBar)
    {
        $this->strBar = $strBar;
    }
}

/**
 * @targetTable agp_listtest_baz.baz_id
 * @module system
 * @moduleId _system_modul_id_
 */
class OrmObjectlistBaz extends OrmObjectlistFoo
{
    /**
     * @var string
     * @tableColumn agp_listtest_baz.baz
     * @tableColumnDatatype char20
     */
    private $strBaz = null;

    /**
     * @return string
     */
    public function getStrBaz()
    {
        return $this->strBaz;
    }

    /**
     * @param string $strBaz
     */
    public function setStrBaz($strBaz)
    {
        $this->strBaz = $strBaz;
    }
}

