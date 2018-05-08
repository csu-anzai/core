<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Carrier;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Rights;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\UserGroup;
use Kajona\System\System\UserUser;

class RightsTest extends Testbase
{

    /**
     * @var Rights
     */
    private $objRights;
    private $strUserId;


    public function testInheritance()
    {
        $objRights = Carrier::getInstance()->getObjRights();
        $this->objRights = Carrier::getInstance()->getObjRights();


        //create a new user & group to be used during testing
        $objUser = new UserUser();
        //$objUser->setStrEmail(generateSystemid()."@".generateSystemid()."de");
        $strUsername = "user_" . generateSystemid();
        $objUser->setStrUsername($strUsername);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objUser))->update($objUser);
        $this->strUserId = $objUser->getSystemid();

        $objGroup = new UserGroup();
        $strName = "name_" . generateSystemid();
        $objGroup->setStrName($strName);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objGroup))->update($objGroup);

        $objGroup->getObjSourceGroup()->addMember($objUser->getObjSourceUser());

        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect);
        $strRootId = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 01");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strRootId);
        $strSecOne = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 02");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strRootId);
        $strSecTwo = $objAspect->getSystemid();

        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 011");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strSecOne);
        $strThirdOne1 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 012");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strSecOne);
        $strThirdOne2 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 021");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strSecTwo);
        $strThirdTwo1 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 022");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strSecTwo);
        $strThirdTwo2 = $objAspect->getSystemid();

        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0111");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdOne1);
        $strThird111 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0112");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdOne1);
        $strThird112 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0121");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdOne2);
        $strThird121 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0122");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdOne2);
        $strThird122 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0211");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdTwo1);
        $strThird211 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0212");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdTwo1);
        $strThird212 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0221");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdTwo2);
        $strThird221 = $objAspect->getSystemid();
        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest 0222");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect, $strThirdTwo2);
        $strThird222 = $objAspect->getSystemid();
        $arrThirdLevelNodes = array($strThird111, $strThird112, $strThird121, $strThird122, $strThird211, $strThird212, $strThird221, $strThird222);


        foreach ($arrThirdLevelNodes as $strOneRootNode) {
            $this->checkNodeRights($strOneRootNode, false, false);
        }

        $objRights->addGroupToRight($objGroup->getSystemid(), $strRootId, "view");
        $objRights->addGroupToRight($objGroup->getSystemid(), $strRootId, "edit");


        foreach ($arrThirdLevelNodes as $strOneRootNode) {
            $this->checkNodeRights($strOneRootNode, true, true);
        }

        $objRights->removeGroupFromRight($objGroup->getSystemid(), $strSecTwo, "view");
        $this->checkNodeRights($strRootId, true, true);
        $this->checkNodeRights($strSecOne, true, true);
        $this->checkNodeRights($strSecTwo, false, true);
        $this->checkNodeRights($strThirdOne1, true, true);
        $this->checkNodeRights($strThirdOne2, true, true);
        $this->checkNodeRights($strThirdTwo1, false, true);
        $this->checkNodeRights($strThirdTwo2, false, true);
        $this->checkNodeRights($strThird111, true, true);
        $this->checkNodeRights($strThird112, true, true);
        $this->checkNodeRights($strThird121, true, true);
        $this->checkNodeRights($strThird122, true, true);
        $this->checkNodeRights($strThird211, false, true);
        $this->checkNodeRights($strThird212, false, true);
        $this->checkNodeRights($strThird221, false, true);
        $this->checkNodeRights($strThird222, false, true);


        $objTempCommons = new SystemAspect($strSecOne);
        $objTempCommons->setStrPrevId($strThird221);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTempCommons))->update($objTempCommons);

        $this->checkNodeRights($strRootId, true, true);
        $this->checkNodeRights($strSecOne, false, true);
        $this->checkNodeRights($strSecTwo, false, true);
        $this->checkNodeRights($strThirdOne1, false, true);
        $this->checkNodeRights($strThirdOne2, false, true);
        $this->checkNodeRights($strThirdTwo1, false, true);
        $this->checkNodeRights($strThirdTwo2, false, true);
        $this->checkNodeRights($strThird111, false, true);
        $this->checkNodeRights($strThird112, false, true);
        $this->checkNodeRights($strThird121, false, true);
        $this->checkNodeRights($strThird122, false, true);
        $this->checkNodeRights($strThird211, false, true);
        $this->checkNodeRights($strThird212, false, true);
        $this->checkNodeRights($strThird221, false, true);
        $this->checkNodeRights($strThird222, false, true);


        $objRights->removeGroupFromRight($objGroup->getSystemid(), $strThirdTwo1, "edit");
        $objRights->addGroupToRight($objGroup->getSystemid(), $strThirdTwo1, "view");
        $this->checkNodeRights($strRootId, true, true);
        $this->checkNodeRights($strSecOne, false, true);
        $this->checkNodeRights($strSecTwo, false, true);
        $this->checkNodeRights($strThirdOne1, false, true);
        $this->checkNodeRights($strThirdOne2, false, true);
        $this->checkNodeRights($strThirdTwo1, true);
        $this->checkNodeRights($strThirdTwo2, false, true);
        $this->checkNodeRights($strThird111, false, true);
        $this->checkNodeRights($strThird112, false, true);
        $this->checkNodeRights($strThird121, false, true);
        $this->checkNodeRights($strThird122, false, true);
        $this->checkNodeRights($strThird211, true);
        $this->checkNodeRights($strThird212, true);
        $this->checkNodeRights($strThird221, false, true);
        $this->checkNodeRights($strThird222, false, true);


        $objTempCommons = new SystemAspect($strThirdOne1);
        $objTempCommons->setStrPrevId($strThird211);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTempCommons))->update($objTempCommons);
        
        $this->checkNodeRights($strRootId, true, true);
        $this->checkNodeRights($strSecOne, false, true);
        $this->checkNodeRights($strSecTwo, false, true);
        $this->checkNodeRights($strThirdOne1, true);
        $this->checkNodeRights($strThirdOne2, false, true);
        $this->checkNodeRights($strThirdTwo1, true);
        $this->checkNodeRights($strThirdTwo2, false, true);
        $this->checkNodeRights($strThird111, true);
        $this->checkNodeRights($strThird112, true);
        $this->checkNodeRights($strThird121, false, true);
        $this->checkNodeRights($strThird122, false, true);
        $this->checkNodeRights($strThird211, true);
        $this->checkNodeRights($strThird212, true);
        $this->checkNodeRights($strThird221, false, true);
        $this->checkNodeRights($strThird222, false, true);


        $objTempCommons = new SystemAspect($strSecOne);
        $objTempCommons->setStrPrevId($strRootId);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTempCommons))->update($objTempCommons);
        //$objSystemCommon->setPrevId($strRootId, $strSecOne); //SecOne still inheriting
        $objTempCommons = new SystemAspect($strThirdOne1);
        $objTempCommons->setStrPrevId($strSecOne);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTempCommons))->update($objTempCommons);
        //$objSystemCommon->setPrevId($strSecOne, $strThirdOne1);
        $objRights->setInherited(true, $strThirdOne1);
        $this->checkNodeRights($strRootId, true, true);
        $this->checkNodeRights($strSecOne, true, true);
        $this->checkNodeRights($strSecTwo, false, true);
        $this->checkNodeRights($strThirdOne1, true, true);
        $this->checkNodeRights($strThirdOne2, true, true);
        $this->checkNodeRights($strThirdTwo1, true);
        $this->checkNodeRights($strThirdTwo2, false, true);
        $this->checkNodeRights($strThird111, true, true);
        $this->checkNodeRights($strThird112, true, true);
        $this->checkNodeRights($strThird121, true, true);
        $this->checkNodeRights($strThird122, true, true);
        $this->checkNodeRights($strThird211, true);
        $this->checkNodeRights($strThird212, true);
        $this->checkNodeRights($strThird221, false, true);
        $this->checkNodeRights($strThird222, false, true);

//        echo "\n\n\n";
//        $this->printTree($strRootId, 1);
//        echo "\n\n\n";

        $objRights->setInherited(true, $strSecTwo);
        $objRights->setInherited(true, $strThirdTwo1);
        $this->checkNodeRights($strRootId, true, true);
        $this->checkNodeRights($strSecOne, true, true);
        $this->checkNodeRights($strSecTwo, true, true);
        $this->checkNodeRights($strThirdOne1, true, true);
        $this->checkNodeRights($strThirdOne2, true, true);
        $this->checkNodeRights($strThirdTwo1, true, true);
        $this->checkNodeRights($strThirdTwo2, true, true);
        $this->checkNodeRights($strThird111, true, true);
        $this->checkNodeRights($strThird112, true, true);
        $this->checkNodeRights($strThird121, true, true);
        $this->checkNodeRights($strThird122, true, true);
        $this->checkNodeRights($strThird211, true, true);
        $this->checkNodeRights($strThird212, true, true);
        $this->checkNodeRights($strThird221, true, true);
        $this->checkNodeRights($strThird222, true, true);


//        echo "\n\n\n";
//        $this->printTree($strRootId, 1);
//        echo "\n\n\n";


        $objAspect->deleteObjectFromDatabase($strThird111);
        $objAspect->deleteObjectFromDatabase($strThird112);
        $objAspect->deleteObjectFromDatabase($strThird121);
        $objAspect->deleteObjectFromDatabase($strThird122);
        $objAspect->deleteObjectFromDatabase($strThird211);
        $objAspect->deleteObjectFromDatabase($strThird212);
        $objAspect->deleteObjectFromDatabase($strThird221);
        $objAspect->deleteObjectFromDatabase($strThird222);

        $objAspect->deleteObjectFromDatabase($strThirdOne1);
        $objAspect->deleteObjectFromDatabase($strThirdOne2);
        $objAspect->deleteObjectFromDatabase($strThirdTwo1);
        $objAspect->deleteObjectFromDatabase($strThirdTwo2);

        $objAspect->deleteObjectFromDatabase($strSecOne);
        $objAspect->deleteObjectFromDatabase($strSecTwo);

        $objAspect->deleteObjectFromDatabase($strRootId);

        $objUser->deleteObjectFromDatabase();
        $objGroup->deleteObjectFromDatabase();

    }


    public function testAddGroupToPermission()
    {
        $objAspect = new SystemAspect();
        $objAspect->setStrName("democase");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect);


        $objGroup = new UserGroup();
        $strName = "name_" . generateSystemid();
        $objGroup->setStrName($strName);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objGroup))->update($objGroup);
        $strGroupId = $objGroup->getSystemid();
        $strGroupShortId = $objGroup->getIntShortId();

        //fill caches
        SystemAspect::getObjectListFiltered();

        $arrRow = Carrier::getInstance()->getObjDB()->getPRow("SELECT * FROM agp_system WHERE system_id = ?", array($objAspect->getSystemid()), 0, false);
        $this->assertFalse(in_array($strGroupShortId, explode(",", $arrRow["right_view"])));
        $this->assertFalse(Carrier::getInstance()->getObjRights()->checkPermissionForGroup($strGroupId, Rights::$STR_RIGHT_VIEW, $objAspect->getSystemid()));

        Carrier::getInstance()->getObjRights()->addGroupToRight($strGroupId, $objAspect->getSystemid(), Rights::$STR_RIGHT_VIEW);

        $arrRow = Carrier::getInstance()->getObjDB()->getPRow("SELECT * FROM agp_system WHERE system_id = ?", array($objAspect->getSystemid()), 0, false);
        $this->assertTrue(in_array($strGroupShortId, explode(",", $arrRow["right_view"])));
        $this->assertTrue(Carrier::getInstance()->getObjRights()->checkPermissionForGroup($strGroupId, Rights::$STR_RIGHT_VIEW, $objAspect->getSystemid()));

        SystemAspect::getObjectListFiltered();

        $arrRow = Carrier::getInstance()->getObjDB()->getPRow("SELECT * FROM agp_system WHERE system_id = ?", array($objAspect->getSystemid()), 0, false);
        $this->assertTrue(in_array($strGroupShortId, explode(",", $arrRow["right_view"])));
        $this->assertTrue(Carrier::getInstance()->getObjRights()->checkPermissionForGroup($strGroupId, Rights::$STR_RIGHT_VIEW, $objAspect->getSystemid()));


        $objAspect->deleteObjectFromDatabase();
        $objGroup->deleteObjectFromDatabase();
    }


    private function checkNodeRights(
        $strNodeId,
        $bitView = false,
        $bitEdit = false,
        $bitDelete = false,
        $bitRights = false,
        $bitRight1 = false,
        $bitRight2 = false,
        $bitRight3 = false,
        $bitRight4 = false,
        $bitRight5 = false
    )
    {

        $this->assertEquals($bitView, $this->objRights->rightView($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights View " . $strNodeId);
        $this->assertEquals($bitEdit, $this->objRights->rightEdit($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Edit " . $strNodeId);
        $this->assertEquals($bitDelete, $this->objRights->rightDelete($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Delete " . $strNodeId);
        $this->assertEquals($bitRights, $this->objRights->rightRight($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Rights" . $strNodeId);
        $this->assertEquals($bitRight1, $this->objRights->rightRight1($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Right1" . $strNodeId);
        $this->assertEquals($bitRight2, $this->objRights->rightRight2($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Right2" . $strNodeId);
        $this->assertEquals($bitRight3, $this->objRights->rightRight3($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Right3" . $strNodeId);
        $this->assertEquals($bitRight4, $this->objRights->rightRight4($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Right4" . $strNodeId);
        $this->assertEquals($bitRight5, $this->objRights->rightRight5($strNodeId, $this->strUserId), __FILE__ . " checkNodeRights Right5" . $strNodeId);

    }

    private function printTree($strRootNode, $intLevel)
    {
        for ($i = 0; $i < $intLevel; $i++)
            echo "   ";

        $objCommon = new SystemAspect($strRootNode);
        //var_dump($objCommon->getSystemRecord());
        echo " / (v: " . $this->objRights->rightView($strRootNode, $this->strUserId) . " e: " . $this->objRights->rightEdit($strRootNode, $this->strUserId) . ") /  " . $objCommon->getSystemid() . "\n";

        //var_dump($objCommon->getChildNodesAsIdArray());
        foreach ($objCommon->getChildNodesAsIdArray() as $strOneId)
            $this->printTree($strOneId, $intLevel + 1);
    }


}

