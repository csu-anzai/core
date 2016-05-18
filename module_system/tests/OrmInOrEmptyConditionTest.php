<?php

namespace Kajona\System\Tests;

use Kajona\System\System\OrmInCondition;
use Kajona\System\System\OrmInOrEmptyCondition;

class OrmInOrEmptyConditionTest extends Testbase
{

    public function testGetStrWhere_InOrEmpty_Empty()
    {
        $arrParams = array();
        $objRestriction = new OrmInOrEmptyCondition("foo", $arrParams);
        $this->assertEquals("", $objRestriction->getStrWhere());

        $arrParams = array(OrmInOrEmptyCondition::NULL_OR_EMPTY);
        $objRestriction = new OrmInOrEmptyCondition("foo", $arrParams);
        $this->assertEquals("((foo IN (?)) OR (foo IS NULL) OR (foo = ''))", $objRestriction->getStrWhere());
    }

    public function testGetStrWhere_InOrEmpty()
    {
        $arrParams = array(1, 2, 3);
        $objRestriction = new OrmInOrEmptyCondition("foo", $arrParams);
        $strWhere = $objRestriction->getStrWhere();
        $this->assertEquals("foo IN (?,?,?)", $objRestriction->getStrWhere());
        $this->assertEquals(count($arrParams), substr_count($strWhere, "?"));


        $arrParams = array(OrmInOrEmptyCondition::NULL_OR_EMPTY, 1, 2, 3);
        $objRestriction = new OrmInOrEmptyCondition("foo", $arrParams);
        $strWhere = $objRestriction->getStrWhere();
        $this->assertEquals("((foo IN (?,?,?,?)) OR (foo IS NULL) OR (foo = ''))", $objRestriction->getStrWhere());
        $this->assertEquals(count($arrParams), substr_count($strWhere, "?"));


        $arrParams = range(0, OrmInCondition::MAX_IN_VALUES);
        $arrParams[] = OrmInOrEmptyCondition::NULL_OR_EMPTY;
        $objRestriction = new OrmInOrEmptyCondition("foo", $arrParams);
        $strWhere = $objRestriction->getStrWhere();

        $this->assertEquals("(((foo IN (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) OR foo IN (?,?))) OR (foo IS NULL) OR (foo = ''))", $strWhere);
        $this->assertEquals(count($arrParams), substr_count($strWhere, "?"));
    }

}

