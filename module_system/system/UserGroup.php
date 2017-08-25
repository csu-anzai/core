<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\System\Usersources\UsersourcesGroupInterface;


/**
 * Model for a user-group, can be based on any type of usersource
 * Groups are NOT represented in the system-table.
 *
 * @package module_user
 * @author sidler@mulchprod.de
 *
 * @module user
 * @moduleId _user_modul_id_
 *
 * @targetTable user_group.group_id
 */
class UserGroup extends Model implements ModelInterface, AdminListableInterface
{

    /**
     * Const to reference the ids generated by the id-generator
     */
    const INT_SHORTID_IDENTIFIER = _user_modul_id_."";


    /**
     * @var string
     * @tableColumn user_group.group_subsystem
     * @tableColumnDatatype char254
     * @tableColumnIndex
     */
    private $strSubsystem = "kajona";

    /**
     * @var string
     * @tableColumn user_group.group_name
     * @tableColumnDatatype char254
     * @tableColumnIndex
     * @listOrder ASC
     */
    private $strName = "";

    /**
     * @var int
     * @tableColumn user_group.group_short_id
     * @tableColumnDatatype int
     */
    private $intShortId = 0;

    /**
     * @var int
     * @tableColumn user_group.group_system_group
     * @tableColumnDatatype int
     */
    private $intSystemGroup = 0;

    /**
     * @var UsersourcesGroupInterface
     */
    private $objSourceGroup;


    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName()
    {
        return $this->getStrName();
    }

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin()
     */
    public function getStrIcon()
    {
        return "icon_group";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo()
    {
        return $this->getNumberOfMembers();
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription()
    {
        $objUsersources = new UserSourcefactory();
        if (count($objUsersources->getArrUsersources()) > 1) {
            $objSubsystem = new UserSourcefactory();
            return $this->getLang("user_list_source", "user")." ".$objSubsystem->getUsersource($this->getStrSubsystem())->getStrReadableName();
        }
        return "";
    }


    /**
     * @inheritDoc
     */
    protected function onInsertToDb()
    {
        Logger::getInstance(Logger::USERSOURCES)->info("saved new group subsystem ".$this->getStrSubsystem()." / ".$this->getStrSystemid());
        //create the new instance on the remote-system
        $objSources = new UserSourcefactory();
        $objProvider = $objSources->getUsersource($this->getStrSubsystem());
        $objTargetGroup = $objProvider->getNewGroup();
        $objTargetGroup->updateObjectToDb();
        $objTargetGroup->setNewRecordId($this->getSystemid());
        $this->objDB->flushQueryCache();

        $this->setIntShortId(IdGenerator::generateNextId(self::INT_SHORTID_IDENTIFIER));

        return true;
    }


    /**
     * Returns all groups from database
     *
     * @param FilterBase $objFilter
     * @param string $strPrevId
     * @param bool|int $intStart
     * @param bool|int $intEnd
     *
     * @return UserGroup[]
     * @static
     */
    public static function getObjectListFiltered(FilterBase $objFilter = null, $strPrevId = "", $intStart = null, $intEnd = null)
    {
        $objOrm = new OrmObjectlist();

        if ($objFilter !== null) {
            $objFilter->addWhereConditionToORM($objOrm);
        }
        return $objOrm->getObjectList(UserGroup::class, "", $intStart, $intEnd);
    }


    /**
     * Fetches the number of groups available
     *
     * @param FilterBase $objFilter
     * @param string $strPrevId
     *
     * @return int
     */
    public static function getObjectCountFiltered(FilterBase $objFilter = null, $strPrevId = "")
    {
        $objOrm = new OrmObjectlist();

        if ($objFilter !== null) {
            $objFilter->addWhereConditionToORM($objOrm);
        }

        return $objOrm->getObjectCount(UserGroup::class);
    }


    /**
     * Returns the number of members of the current group.
     *
     * @return int
     */
    public function getNumberOfMembers()
    {
        $this->loadSourceObject();
        return $this->objSourceGroup->getNumberOfMembers();
    }

    /**
     * Deletes the given group
     *
     * @return bool
     */
    public function deleteObjectFromDatabase()
    {
        Logger::getInstance(Logger::USERSOURCES)->warning("deleted group with id ".$this->getSystemid()." (".$this->getStrName().")");
        //Delete related group
        $this->getObjSourceGroup()->deleteGroup();
        return parent::deleteObjectFromDatabase();
    }

    /**
     * Loads the mapped source-object
     */
    private function loadSourceObject()
    {
        if ($this->objSourceGroup == null) {
            $objUsersources = new UserSourcefactory();
            $this->setObjSourceGroup($objUsersources->getSourceGroup($this));
        }
    }

    /**
     * Loads a group by its name, returns null of not found
     *
     * @param string $strName
     *
     * @return UserGroup
     */
    public static function getGroupByName($strName)
    {
        $objFactory = new UserSourcefactory();
        return $objFactory->getGroupByName($strName);
    }

    /**
     * Returns the short-id for a single group, cached lookups are supported.
     * If the cache is filled, the id is fetched without an instantiation.
     *
     * @param string $strGroupId
     * @return int|null
     */
    public static function getShortIdForGroupId(string $strGroupId)
    {
        //build the map, cached by the cache manager. on cache-miss rebuilding.
        /** @var CacheManager $objCache */
        $objCache = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_CACHE_MANAGER);
        $strCacheKey = __CLASS__."group2id";
        $arrIds = $objCache->getValue($strCacheKey);
        if ($arrIds === false) {
            $arrIds = array();
        }

        if (array_key_exists($strGroupId, $arrIds)) {
            return $arrIds[$strGroupId];
        }

        //not found in cache, refill
        foreach (Carrier::getInstance()->getObjDB()->getPArray("SELECT group_id, group_short_id FROM "._dbprefix_."user_group", array()) as $arrOneRow) {
            $arrIds[$arrOneRow["group_id"]] = $arrOneRow["group_short_id"];
        }

        if (!array_key_exists($strGroupId, $arrIds)) {
            $arrIds[$strGroupId] = null;
        }
        $objCache->addValue($strCacheKey, $arrIds, 0);
        return $arrIds[$strGroupId];
    }

    /**
     * Returns the systemid for a groups short-id
     *
     * @param int $intShortid
     * @return mixed
     */
    public static function getGroupIdForShortId(int $intShortid)
    {
        /** @var CacheManager $objCache */
        $objCache = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_CACHE_MANAGER);
        $strCacheKey = __CLASS__."id2group";
        $arrIds = $objCache->getValue($strCacheKey);
        if ($arrIds === false) {
            $arrIds = array();
        }

        if (array_key_exists($intShortid, $arrIds)) {
            return $arrIds[$intShortid];
        }

        //fill the cache completely
        foreach (Carrier::getInstance()->getObjDB()->getPArray("SELECT group_id, group_short_id FROM "._dbprefix_."user_group", array()) as $arrOneRow) {
            $arrIds[$arrOneRow["group_short_id"]] = $arrOneRow["group_id"];
        }

        if (!array_key_exists($intShortid, $arrIds)) {
            $arrIds[$intShortid] = null;
        }

        $objCache->addValue($strCacheKey, $arrIds, 0);
        return $arrIds[$intShortid];
    }

    public function getStrSubsystem()
    {
        return $this->strSubsystem;
    }

    public function setStrSubsystem($strSubsystem)
    {
        $this->strSubsystem = $strSubsystem;
    }

    /**
     * @return UsersourcesGroupInterface
     */
    public function getObjSourceGroup()
    {
        $this->loadSourceObject();
        return $this->objSourceGroup;
    }

    public function setObjSourceGroup($objSourceGroup)
    {
        $this->objSourceGroup = $objSourceGroup;
    }

    public function getStrName()
    {
        return $this->strName;
    }

    public function setStrName($strName)
    {
        $this->strName = $strName;
    }

    public function getIntRecordStatus()
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getIntShortId()
    {
        return $this->intShortId;
    }

    /**
     * @param int $intShortId
     * @return UserGroup
     */
    public function setIntShortId($intShortId)
    {
        $this->intShortId = $intShortId;
        return $this;
    }

    /**
     * @return int
     */
    public function getIntSystemGroup(): int
    {
        return $this->intSystemGroup;
    }

    /**
     * @param int $intSystemGroup
     */
    public function setIntSystemGroup(int $intSystemGroup)
    {
        $this->intSystemGroup = $intSystemGroup;
    }



}
