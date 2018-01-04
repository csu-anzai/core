<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security;

use Kajona\System\System\Date;
use Kajona\System\System\Lang;
use Kajona\System\System\Link;
use Kajona\System\System\Mail;
use Kajona\System\System\SystemPwchangehistory;
use Kajona\System\System\SystemPwHistory;
use Kajona\System\System\Usersources\UsersourcesUserKajona;
use Kajona\System\System\UserUser;

/**
 * Service which sends a reminder to users to change the password after a specific amount of days
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class PasswordRotator
{
    /**
     * @var Lang
     */
    protected $objLang;

    /**
     * @var int
     */
    protected $intDays;

    /**
     * @param Lang $objLang
     * @param int $intDays
     */
    public function __construct(Lang $objLang, $intDays = null)
    {
        $this->objLang = $objLang;
        $this->intDays = $intDays;
    }

    /**
     * @throws \Kajona\System\System\Exception
     */
    public function rotate()
    {
        // in case we have no days from the config we dont rotate passwords
        if (empty($this->intDays)) {
            return;
        }

        $arrUsers = SystemPwHistory::getPendingUsers($this->intDays);

        foreach ($arrUsers as $objUser) {
            if ($this->isPasswordExpired($objUser)) {
                $this->sendResetPassword($objUser);
            }
        }
    }

    /**
     * @param UserUser $objUser
     * @param int $intDays
     * @return bool
     */
    public function isPasswordExpired(UserUser $objUser)
    {
        // ignore deleted users
        if ($objUser->getIntRecordDeleted() == 1) {
            return false;
        }

        // ignore non-active users
        if ($objUser->getIntRecordStatus() != 1) {
            return false;
        }

        $objInternalUser = $objUser->getObjSourceUser();
        if ($objInternalUser instanceof UsersourcesUserKajona) {
            $objNow = new Date();
            $objChangeDate = SystemPwHistory::getLastChangeDate($objUser);
            if ($objChangeDate instanceof Date) {
                $intDiff = $objNow->getTimeInOldStyle() - $objChangeDate->getTimeInOldStyle();
                return $intDiff > ((60 * 60 * 24) * $this->intDays);
            }
        }

        return false;
    }

    /**
     * @param UserUser $objUser
     * @param bool $bitShowUserNameInMail
     * @throws \Kajona\System\System\Exception
     */
    public function sendResetPassword(UserUser $objUser, $bitShowUserNameInMail = true)
    {
        // add a one-time token and reset the password
        $strToken = generateSystemid();
        $objUser->setStrAuthcode($strToken);
        $objUser->updateObjectToDb();

        // @TODO change if we have a $strLang argument for the Lang::getLang method
        $strLang = $this->objLang->getStrTextLanguage();
        $this->objLang->setStrTextLanguage($objUser->getStrAdminlanguage());

        // send mail
        $strActivationLink = Link::getLinkAdminHref("login", "pwdReset", ["systemid" => $objUser->getSystemid(), "authcode" => $strToken], false);

        $objMail = new Mail();
        $objMail->addTo($objUser->getStrEmail());
        $objMail->setSubject($this->objLang->getLang("user_password_resend_subj", "user"));

        if ($bitShowUserNameInMail) {
            $objMail->setText($this->objLang->getLang("user_password_resend_body_username", "user", [$objUser->getStrUsername(), $strActivationLink]));
        } else {
            $objMail->setText($this->objLang->getLang("user_password_resend_body", "user", [$strActivationLink]));
        }

        $objMail->sendMail();

        // @TODO change if we have a $strLang argument for the Lang::getLang method
        $this->objLang->setStrTextLanguage($strLang);

        // insert pw change history log entry
        $objNow = new Date();
        $objPwChange = new SystemPwchangehistory();
        $objPwChange->setStrTargetUser($objUser->getStrSystemid());
        $objPwChange->setStrActivationLink($strActivationLink);
        $objPwChange->setStrChangeDate($objNow->getLongTimestamp());
        $objPwChange->updateObjectToDb();
    }
}
