<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\System\Usersources\UsersourcesSourceKajona;
use Kajona\System\System\Usersources\UsersourcesUserKajona;


/**
 * @author christoph.kappestein@gmail.com
 *
 * @targetTable user_pwhistory.id
 * @module system
 * @moduleId _system_modul_id_
 */
class SystemPwHistory extends Model implements ModelInterface, AdminListableInterface
{
    /**
     * @var string
     * @tableColumn user_pwhistory.history_targetuser
     * @tableColumnDatatype char20
     * @tableColumnIndex
     */
    protected $strTargetUser;

    /**
     * @var string
     * @tableColumn user_pwhistory.history_pass
     * @tableColumnDatatype char254
     * @tableColumnIndex
     */
    protected $strPass;

    /**
     * @var string
     * @tableColumn user_pwhistory.history_changedate
     * @tableColumnDatatype long
     * @tableColumnIndex
     */
    protected $strChangeDate;

    /**
     * @return mixed
     */
    public function getStrTargetUser()
    {
        return $this->strTargetUser;
    }

    /**
     * @param $strTargetUser
     */
    public function setStrTargetUser($strTargetUser)
    {
        $this->strTargetUser = $strTargetUser;
    }

    /**
     * @return string
     */
    public function getStrPass()
    {
        return $this->strPass;
    }

    /**
     * @param string $strPass
     */
    public function setStrPass($strPass)
    {
        $this->strPass = $strPass;
    }

    /**
     * @return mixed
     */
    public function getStrChangeDate()
    {
        return $this->strChangeDate;
    }

    /**
     * @param mixed $strChangeDate
     */
    public function setStrChangeDate($strChangeDate)
    {
        $this->strChangeDate = $strChangeDate;
    }

    public function getStrDisplayName()
    {
        $objUser = Objectfactory::getInstance()->getObject($this->getStrOwner());
        if($objUser !== null) {
            return $objUser->getStrDisplayName() . " (" . dateToString($this->getStrChangeDate()) . ")";
        }
        return "";
    }

    public function getStrIcon()
    {
        return "icon_user";
    }

    public function getStrAdditionalInfo()
    {
        return "";
    }

    public function getStrLongDescription()
    {
        return "";
    }

    /**
     * Returns whether the provided password was already used by the user in the past. The length parameter specifies
     * the last x used password which are checked
     *
     * @param UserUser $objUser
     * @param string $strPassword
     * @param int $intLength
     * @return bool
     */
    public static function isPasswordInHistory(UserUser $objUser, $strPassword, $intLength)
    {
        $objInteralUser = $objUser->getObjSourceUser();
        if ($objInteralUser instanceof UsersourcesUserKajona) {
            $strPrefix = _dbprefix_;
            $strQuery = "SELECT history_pass FROM {$strPrefix}user_pwhistory WHERE history_targetuser = ? ORDER BY history_changedate DESC";
            $arrPwHistory = Database::getInstance()->getPArray($strQuery, [$objUser->getSystemid()], 0, $intLength);

            foreach ($arrPwHistory as $arrRow) {
                /** @var SystemPwHistory $objPwHistory */
                $strPass = UsersourcesSourceKajona::encryptPassword($strPassword, $objInteralUser->getStrSalt());
                if ($strPass == $arrRow["history_pass"]) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns all users which need to change the password
     *
     * @param int $intOffset
     * @return UserUser[]
     */
    public static function getPendingUsers(int $intOffset)
    {
        $objNow = new Date();
        $strPrefix = _dbprefix_;
        $strQuery = "SELECT history_targetuser FROM {$strPrefix}user_pwhistory GROUP BY history_targetuser HAVING MAX(history_changedate) + ? < ?";
        $arrPwHistory = Database::getInstance()->getPArray($strQuery, [$intOffset, $objNow->getLongTimestamp()]);
        $arrUsers = [];

        foreach ($arrPwHistory as $arrRow) {
            /** @var SystemPwHistory $objPwHistory */
            $objUser = Objectfactory::getInstance()->getObject($arrRow["history_targetuser"]);

            if ($objUser instanceof UserUser) {
                // ignore deleted users
                if ($objUser->getIntRecordDeleted() == 1) {
                    continue;
                }

                // only active users
                if ($objUser->getIntRecordStatus() != 1) {
                    continue;
                }

                // check whether source is still kajona
                $objInteralUser = $objUser->getObjSourceUser();
                if (!$objInteralUser instanceof UsersourcesUserKajona) {
                    continue;
                }

                $arrUsers[] = $objUser;
            }
        }

        return $arrUsers;
    }
}
