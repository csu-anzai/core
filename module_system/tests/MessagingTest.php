<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Database;
use Kajona\System\System\Date;
use Kajona\System\System\Messageproviders\MessageproviderExceptions;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\MessagingMessagehandler;
use Kajona\System\System\MessagingQueue;
use Kajona\System\System\SystemSetting;
use Kajona\System\System\UserGroup;
use Kajona\System\System\UserUser;

class MessagingTest extends Testbase
{
    public function setUp()
    {
        parent::setUp();

        $this->removeAllQueueEntries();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->removeAllQueueEntries();
    }

    public function testSendMessage()
    {
        $strText = generateSystemid() . " autotest";
        $strTitle = generateSystemid() . " title";
        $strIdentifier = generateSystemid() . " identifier";

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessage($strText, new UserGroup(SystemSetting::getConfigValue("_admins_group_id_")), new MessageproviderExceptions(), $strIdentifier, $strTitle);

        $bitFound = false;

        $objGroup = new UserGroup(SystemSetting::getConfigValue("_admins_group_id_"));
        $arrUsers = $objGroup->getObjSourceGroup()->getUserIdsForGroup();

        $arrMessages = MessagingMessage::getObjectListFiltered(null, $arrUsers[0]);

        foreach ($arrMessages as $objOneMessage) {
            if ($objOneMessage->getStrBody() == $strText && $objOneMessage->getStrMessageProvider() == "Kajona\\System\\System\\Messageproviders\\MessageproviderExceptions") {
                $bitFound = true;
                $this->assertEquals($objOneMessage->getStrTitle(), $strTitle);
                $this->assertEquals($objOneMessage->getStrInternalIdentifier(), $strIdentifier);
                $this->assertTrue($objOneMessage->deleteObjectFromDatabase());
            }
        }

        $this->assertTrue($bitFound);
        $this->flushDBCache();
    }

    public function testSendMessageObject()
    {
        $strText = generateSystemid() . " autotest";
        $strTitle = generateSystemid() . " title";
        $strIdentifier = generateSystemid() . " identifier";
        $strSender = generateSystemid();
        $strReference = generateSystemid();

        $objMessage = new MessagingMessage();
        $objMessage->setStrTitle($strTitle);
        $objMessage->setStrBody($strText);
        $objMessage->setStrInternalIdentifier($strIdentifier);
        $objMessage->setObjMessageProvider(new MessageproviderExceptions());
        $objMessage->setStrSenderId($strSender);
        $objMessage->setStrMessageRefId($strReference);

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage, new UserGroup(SystemSetting::getConfigValue("_admins_group_id_")));


        $objGroup = new UserGroup(SystemSetting::getConfigValue("_admins_group_id_"));
        $arrUsers = $objGroup->getObjSourceGroup()->getUserIdsForGroup();

        foreach ($arrUsers as $objOneUser) {
            $bitFound = false;
            $arrMessages = MessagingMessage::getObjectListFiltered(null, $objOneUser);

            foreach ($arrMessages as $objOneMessage) {
                if ($objOneMessage->getStrBody() == $strText && $objOneMessage->getStrMessageProvider() == "Kajona\\System\\System\\Messageproviders\\MessageproviderExceptions") {
                    $bitFound = true;
                    $this->assertEquals($objOneMessage->getStrTitle(), $strTitle);
                    $this->assertEquals($objOneMessage->getStrInternalIdentifier(), $strIdentifier);
                    $this->assertEquals($objOneMessage->getStrSenderId(), $strSender);
                    $this->assertEquals($objOneMessage->getStrMessageRefId(), $strReference);
                    $this->assertTrue($objOneMessage->deleteObjectFromDatabase());
                }
            }


            $this->assertTrue($bitFound);
        }

        $this->flushDBCache();
    }

    public function testSendMessageObjectWithSendDateFuture()
    {
        $strText = generateSystemid() . " autotest";
        $strTitle = generateSystemid() . " title";
        $strIdentifier = generateSystemid() . " identifier";
        $strSender = generateSystemid();
        $strReference = generateSystemid();
        $objSendDate = new Date();
        $objSendDate->setNextDay();

        $objMessage = new MessagingMessage();
        $objMessage->setStrTitle($strTitle);
        $objMessage->setStrBody($strText);
        $objMessage->setStrInternalIdentifier($strIdentifier);
        $objMessage->setObjMessageProvider(new MessageproviderExceptions());
        $objMessage->setStrSenderId($strSender);
        $objMessage->setStrMessageRefId($strReference);

        $objUser = UserUser::getAllUsersByName("user")[0];

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage, $objUser, $objSendDate);

        $arrQueue = MessagingQueue::getObjectListFiltered();
        $this->assertEquals(1, count($arrQueue));

        $arrMessages = MessagingMessage::getMessagesByIdentifier($strIdentifier);
        $this->assertEquals(0, count($arrMessages));

        /** @var MessagingQueue $objQueue */
        $objQueue = $arrQueue[0];

        // the sender sets the date to the beginning of the day
        $objSendDate->setBeginningOfDay();

        $this->assertInstanceOf(MessagingQueue::class, $objQueue);
        $this->assertEquals($objUser->getSystemid(), $objQueue->getStrReceiver());
        $this->assertEquals($objSendDate->getLongTimestamp(), $objQueue->getObjSendDate()->getLongTimestamp());

        $objMessage = $objQueue->getMessage();

        $this->assertInstanceOf(MessagingMessage::class, $objMessage);
        $this->assertEquals($strTitle, $objMessage->getStrTitle());
        $this->assertEquals($strText, $objMessage->getStrBody());
        $this->assertEquals($strIdentifier, $objMessage->getStrInternalIdentifier());
        $this->assertEquals(MessageproviderExceptions::class, $objMessage->getStrMessageProvider());
        $this->assertEquals($strSender, $objMessage->getStrSenderId());
        $this->assertEquals($strReference, $objMessage->getStrMessageRefId());
    }

    public function testSendMessageObjectWithSendDateNow()
    {
        $strText = generateSystemid() . " autotest";
        $strTitle = generateSystemid() . " title";
        $strIdentifier = generateSystemid() . " identifier";
        $strSender = generateSystemid();
        $strReference = generateSystemid();
        $objSendDate = new Date();

        $objMessage = new MessagingMessage();
        $objMessage->setStrTitle($strTitle);
        $objMessage->setStrBody($strText);
        $objMessage->setStrInternalIdentifier($strIdentifier);
        $objMessage->setObjMessageProvider(new MessageproviderExceptions());
        $objMessage->setStrSenderId($strSender);
        $objMessage->setStrMessageRefId($strReference);

        $objUser = UserUser::getAllUsersByName("user")[0];

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage, $objUser, $objSendDate);

        $arrQueue = MessagingQueue::getObjectListFiltered();
        $this->assertEquals(0, count($arrQueue));

        $arrMessages = MessagingMessage::getMessagesByIdentifier($strIdentifier);
        $this->assertEquals(1, count($arrMessages));

        /** @var MessagingQueue $objQueue */
        $objMessage = $arrMessages[0];

        $this->assertInstanceOf(MessagingMessage::class, $objMessage);
        $this->assertEquals($strTitle, $objMessage->getStrTitle());
        $this->assertEquals($strText, $objMessage->getStrBody());
        $this->assertEquals($strIdentifier, $objMessage->getStrInternalIdentifier());
        $this->assertEquals(MessageproviderExceptions::class, $objMessage->getStrMessageProvider());
        $this->assertEquals($strSender, $objMessage->getStrSenderId());
        $this->assertEquals($strReference, $objMessage->getStrMessageRefId());
    }

    public function testUnreadCount()
    {
        $strText = generateSystemid() . " autotest";

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessage($strText, new UserGroup(SystemSetting::getConfigValue("_admins_group_id_")), new MessageproviderExceptions());

        $bitFound = false;

        $objGroup = new UserGroup(SystemSetting::getConfigValue("_admins_group_id_"));
        $arrUsers = $objGroup->getObjSourceGroup()->getUserIdsForGroup();

        $arrMessages = MessagingMessage::getObjectListFiltered(null, $arrUsers[0]);

        $intUnread = MessagingMessage::getNumberOfMessagesForUser($arrUsers[0], true);

        $this->assertTrue($intUnread > 0);
        $this->flushDBCache();

        foreach ($arrMessages as $objOneMessage) {
            if ($objOneMessage->getStrBody() == $strText && $objOneMessage->getStrMessageProvider() == "Kajona\\System\\System\\Messageproviders\\MessageproviderExceptions") {
                $bitFound = true;
                $objOneMessage->setBitRead(true);
                $objOneMessage->updateObjectToDb();

                $this->assertEquals($intUnread - 1, MessagingMessage::getNumberOfMessagesForUser($arrUsers[0], true));


                $objOneMessage->deleteObjectFromDatabase();
            }
        }

        $this->assertTrue($bitFound);
        $this->flushDBCache();
    }

    private function removeAllQueueEntries()
    {
        $strPrefix = _dbprefix_;
        Database::getInstance()->_pQuery("DELETE FROM {$strPrefix}messages_queue WHERE 1=1", []);
    }
}

