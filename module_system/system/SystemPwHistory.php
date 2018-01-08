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
     * @param UsersourcesUserKajona $objUser
     * @param string $strPassword
     * @param int $intLength
     * @return bool
     */
    public static function isPasswordInHistory(UsersourcesUserKajona $objUser, $strPassword, $intLength)
    {
        $strPrefix = _dbprefix_;
        $strQuery = "SELECT history_pass FROM {$strPrefix}user_pwhistory WHERE history_targetuser = ? ORDER BY history_changedate DESC";
        $arrPwHistory = Database::getInstance()->getPArray($strQuery, [$objUser->getSystemid()], 0, $intLength);

        foreach ($arrPwHistory as $arrRow) {
            /** @var SystemPwHistory $objPwHistory */
            $strPass = UsersourcesSourceKajona::encryptPassword($strPassword, $objUser->getStrSalt());
            if ($strPass == $arrRow["history_pass"]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param UsersourcesUserKajona $objUser
     * @return Date|null
     */
    public static function getLastChangeDate(UsersourcesUserKajona $objUser)
    {
        $strPrefix = _dbprefix_;
        $strQuery = "SELECT history_changedate FROM {$strPrefix}user_pwhistory WHERE history_targetuser = ? ORDER BY history_changedate DESC";
        $arrRow = Database::getInstance()->getPRow($strQuery, [$objUser->getSystemid()]);
        if (isset($arrRow["history_changedate"])) {
            return new Date($arrRow["history_changedate"]);
        } else {
            return null;
        }
    }
}
