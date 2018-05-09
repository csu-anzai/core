<?php

namespace Kajona\Tags\Tests;

use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\SystemAspect;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\UserGroup;
use Kajona\System\System\UserUser;
use Kajona\System\Tests\Testbase;
use Kajona\Tags\System\TagsTag;

class TagsTest extends Testbase
{


    /**
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleCopyException
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleDeleteException
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function testCopyRecordWithTag()
    {

        $objAspect = new SystemAspect();
        $objAspect->setStrName("autotest");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect);

        $objTag = new TagsTag();
        $objTag->setStrName("demo tag");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTag))->update($objTag);

        $objTag->assignToSystemrecord($objAspect->getStrSystemid());

        $objFirstAspect = new SystemAspect($objAspect->getSystemid());

        $objAspect = ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->copy($objAspect);

        $this->assertNotEquals($objFirstAspect->getSystemid(), $objAspect->getSystemid());

        $this->assertEquals(count(TagsTag::getTagsForSystemid($objFirstAspect->getSystemid())), count(TagsTag::getTagsForSystemid($objAspect->getSystemid())));

        $arrTagsFirst = TagsTag::getTagsForSystemid($objFirstAspect->getSystemid());
        $objFirstTag = $arrTagsFirst[0];
        $arrTagsCopy = TagsTag::getTagsForSystemid($objAspect->getSystemid());
        $objSecondTag = $arrTagsCopy[0];

        $this->assertEquals($objFirstTag->getSystemid(), $objSecondTag->getSystemid());


        ServiceLifeCycleFactory::getLifeCycle(get_class($objFirstAspect))->deleteObjectFromDatabase($objFirstAspect);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->deleteObjectFromDatabase($objAspect);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objSecondTag))->deleteObjectFromDatabase($objSecondTag);
    }


    /**
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleDeleteException
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function testTagAssignmentRemoval()
    {
        //related to checkin #6111

        $objTag = new TagsTag();
        $objTag->setStrName(generateSystemid());
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTag))->update($objTag);

        $objAspect = new SystemAspect();
        $objAspect->setStrName(generateSystemid());
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->update($objAspect);

        $objTag->assignToSystemrecord($objAspect->getSystemid());

        $this->flushDBCache();

        $this->assertEquals(count($objTag->getArrAssignedRecords()), 1);
        $this->assertEquals(count(TagsTag::getTagsForSystemid($objAspect->getSystemid())), 1);

        $objTag->removeFromSystemrecord($objAspect->getSystemid(), "");

        $this->flushDBCache();

        $this->assertEquals(count($objTag->getArrAssignedRecords()), 0);
        $this->assertEquals(count(TagsTag::getTagsForSystemid($objAspect->getSystemid())), 0);

        ServiceLifeCycleFactory::getLifeCycle(get_class($objTag))->deleteObjectFromDatabase($objTag);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objAspect))->deleteObjectFromDatabase($objAspect);
    }


    /**
     * @throws \Kajona\System\System\Exception
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function testTagAssignment()
    {
        $strName = generateSystemid();
        $arrUsergroups = UserGroup::getObjectListFiltered();

        if (count($arrUsergroups) == 0) {
            return;
        }

        $objTag = new TagsTag();
        $objTag->setStrName($strName);
        ServiceLifeCycleFactory::getLifeCycle(get_class($objTag))->update($objTag);


        foreach ($arrUsergroups as $objOnePage) {
            $objTag->assignToSystemrecord($objOnePage->getSystemid());
            break;
        }

        $arrUsers = UserUser::getObjectListFiltered();
        foreach ($arrUsers as $objUser) {
            $objTag->assignToSystemrecord($objUser->getSystemid());
            break;
        }


        $this->flushDBCache();

        $objTag = TagsTag::getTagByName($strName);
        $this->assertEquals($objTag->getIntAssignments(), 2);

        $arrPlainAssignments = $objTag->getListOfAssignments();
        $this->assertEquals(count($arrPlainAssignments), 2);

        $arrAssignment = $objTag->getArrAssignedRecords();
        $this->assertEquals(count($arrAssignment), 2);

        $this->assertTrue($arrAssignment[0] instanceof UserGroup || $arrAssignment[0] instanceof UserUser);
        $this->assertTrue($arrAssignment[1] instanceof UserGroup || $arrAssignment[1] instanceof UserUser);


        $strOldSysid = $objTag->getSystemid();
        $objTag->copyObject();

        $this->assertNotEquals($strOldSysid, $objTag->getSystemid());
        $this->assertEquals($objTag->getStrName(), $strName . "_1");

        $this->assertEquals($objTag->getIntAssignments(), 2);
        $arrAssignment = $objTag->getArrAssignedRecords();

        $this->assertEquals(count($arrAssignment), 2);

        $this->assertTrue($arrAssignment[0] instanceof UserGroup || $arrAssignment[0] instanceof UserUser);
        $this->assertTrue($arrAssignment[1] instanceof UserGroup || $arrAssignment[1] instanceof UserUser);

        $objTag->deleteObjectFromDatabase();
    }


}

