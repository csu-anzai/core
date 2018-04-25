<?php
/*"******************************************************************************************************
*   (c) 2007-2018 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;

/**
 * The lockmanager takes care of locking and unlocking systemrecords.
 * It provides the methods to check if a record is locked or not.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 3.3.0
 */
class Lockmanager
{

    /**
     * @var Root
     */
    private $objSourceObject = null;

    /**
     * Constructor
     *
     * @param Root|null $objSourceObject
     * @internal use the Root::getLockmanager() call instead
     */
    public function __construct(Root $objSourceObject)
    {
        $this->objSourceObject = $objSourceObject;
    }

    /**
     * Locks a systemrecord for the current user
     *
     * @return bool
     */
    public function lockRecord()
    {
        $strQuery = "UPDATE agp_system
						SET system_lock_id = ?,
						    system_lock_time = ?
						WHERE system_id =?";

        $intLockTime = time();
        if (Carrier::getInstance()->getObjDB()->_pQuery($strQuery, array(Carrier::getInstance()->getObjSession()->getUserID(), $intLockTime, $this->objSourceObject->getSystemid()))) {
            $this->objSourceObject->setStrLockId(Carrier::getInstance()->getObjSession()->getUserID());
            $this->objSourceObject->setIntLockTime($intLockTime);
            return true;
        }

        return false;
    }

    /**
     * Checks if the current record is locked, ignoring the locking user-id.
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->getLockedUntilTimestamp(true) > time();
    }

    /**
     * Unlocks a dataRecord as long as the record is locked by the current one
     *
     * @param bool $bitForceUnlock unlocks the record, even if the user is not the owner of the lock.
     *
     * @return bool
     * @throws Exception
     */
    public function unlockRecord($bitForceUnlock = false)
    {
        if ($bitForceUnlock || $this->isLockedByCurrentUser()) {
            $strQuery = "UPDATE agp_system
                            SET system_lock_time = '0'
                            WHERE system_id=? ";
            if (Carrier::getInstance()->getObjDB()->_pQuery($strQuery, array($this->objSourceObject->getSystemid()))) {
                $this->objSourceObject->setStrLockId("");
                $this->objSourceObject->setIntLockTime(0);
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the record is locked for the current user and so accessible.
     * If the record is locked by someone else, false will be returned, true otherwise.
     *
     * @return bool
     * @throws Exception
     */
    public function isAccessibleForCurrentUser()
    {
        $intLockedUntil = $this->getLockedUntilTimestamp();
        //lock is already outdated
        if ($intLockedUntil < time()) {
            return true;
        }

        //lock not outdated, so validate the owner-id
        $strLockId = $this->getLockId();
        if (validateSystemid($strLockId)) {
            if ($strLockId != Carrier::getInstance()->getObjSession()->getUserID()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks of the current record is locked exclusively for the current user,
     * so the lock is being held by the current user.
     *
     * @return bool
     * @throws Exception
     */
    public function isLockedByCurrentUser()
    {
        $intLockedUntil = $this->getLockedUntilTimestamp();
        //lock is already outdated
        if ($intLockedUntil < time()) {
            return false;
        }

        $strLockId = $this->getLockId();
        if (validateSystemid($strLockId)) {
            if ($strLockId == Carrier::getInstance()->getObjSession()->getUserID()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines whether the current user is allowed to unlock a record or not.
     * This is only the case, if the user is member of the admin-group.
     *
     * @return bool
     * @throws Exception
     */
    public function isUnlockableForCurrentUser()
    {

        if (Carrier::getInstance()->getObjSession()->isSuperAdmin()) {
            return true;
        }

        return false;
    }


    /**
     * Unlocks records locked passed the defined max-locktime
     *
     * @return bool
     * @deprecated this method is no longer used
     */
    public function unlockOldRecords()
    {
        return true;
    }

    /**
     * Fetches the current user-id locking the record
     *
     * @return string
     */
    public function getLockId()
    {
        if (validateSystemid($this->objSourceObject->getSystemid()) && $this->objSourceObject->getStrLockId() != "") {
            return $this->objSourceObject->getStrLockId();
        } else {
            return "0";
        }
    }


    /**
     * Fetches the current user-id locking the record
     *
     * @param bool $bitIgnoreLockId
     * @return string
     */
    private function getLockedUntilTimestamp($bitIgnoreLockId = false)
    {
        if (validateSystemid($this->objSourceObject->getSystemid()) && ($bitIgnoreLockId || $this->objSourceObject->getStrLockId() != "")) {
            return $this->objSourceObject->getIntLockTime() + (int)SystemSetting::getConfigValue("_system_lock_maxtime_");
        } else {
            return "0";
        }
    }


    /**
     * Fetches a list of records currently locked in the database
     *
     * @param null|int $intStart
     * @param null|int $intEnd
     *
     * @return Model[]
     */
    public static function getLockedRecords($intStart = null, $intEnd = null)
    {
        $strQuery = "SELECT system_id FROM agp_system WHERE system_lock_time > ? ORDER BY system_id DESC";
        $arrRows = Carrier::getInstance()->getObjDB()->getPArray($strQuery, array(time() - (int)SystemSetting::getConfigValue("_system_lock_maxtime_")), $intStart, $intEnd);

        $arrReturn = array();
        foreach ($arrRows as $arrOneRow) {
            $arrReturn[] = Objectfactory::getInstance()->getObject($arrOneRow["system_id"]);
        }

        return $arrReturn;
    }

    /**
     * Fetches a list of records currently locked in the database
     *
     * @param string $strUserId
     *
     * @return Model[]
     */
    public static function getLockedRecordsForUser($strUserId)
    {
        $strQuery = "SELECT system_id FROM agp_system WHERE system_lock_id = ? AND system_lock_time > ? ORDER BY system_id DESC";
        $arrRows = Carrier::getInstance()->getObjDB()->getPArray($strQuery, array($strUserId, time() - (int)SystemSetting::getConfigValue("_system_lock_maxtime_")));

        $arrReturn = array();
        foreach ($arrRows as $arrOneRow) {
            $arrReturn[] = Objectfactory::getInstance()->getObject($arrOneRow["system_id"]);
        }

        return $arrReturn;
    }

    /**
     * Counts the number of records currently locked in the database
     *
     * @return int
     */
    public static function getLockedRecordsCount()
    {
        $strQuery = "SELECT COUNT(*) AS cnt FROM agp_system WHERE system_lock_time > ?";
        $arrRow = Carrier::getInstance()->getObjDB()->getPRow($strQuery, array(time() - (int)SystemSetting::getConfigValue("_system_lock_maxtime_")));
        return $arrRow["cnt"];
    }
}
