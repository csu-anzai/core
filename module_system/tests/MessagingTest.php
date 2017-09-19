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

        $objGroup = new UserGroup(SystemSetting::getConfigValue("_admins_group_id_"));
        $arrUsers = $objGroup->getObjSourceGroup()->getUserIdsForGroup();

        $arrMessages = MessagingMessage::getObjectListFiltered(null, $arrUsers[0]);

        $bitFound = false;
        foreach ($arrMessages as $objOneMessage) {
            $bitFound = true;
            $this->assertEquals($strTitle, $objOneMessage->getStrTitle());
            $this->assertEquals($strText, $objOneMessage->getStrBody());
            $this->assertEquals($strIdentifier, $objOneMessage->getStrInternalIdentifier());
        }

        $this->assertTrue($bitFound);
        $this->flushDBCache();
    }

    public function testSendMessageObject()
    {
        $objMessage = $this->newMessage();

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage, new UserGroup(SystemSetting::getConfigValue("_admins_group_id_")));

        $objGroup = new UserGroup(SystemSetting::getConfigValue("_admins_group_id_"));
        $arrUsers = $objGroup->getObjSourceGroup()->getUserIdsForGroup();

        foreach ($arrUsers as $objOneUser) {
            $bitFound = false;
            $arrMessages = MessagingMessage::getObjectListFiltered(null, $objOneUser);

            foreach ($arrMessages as $objOneMessage) {
                $bitFound = true;
                $this->assertEquals($objMessage->getStrTitle(), $objOneMessage->getStrTitle());
                $this->assertEquals($objMessage->getStrInternalIdentifier(), $objOneMessage->getStrInternalIdentifier());
                $this->assertEquals($objMessage->getStrSenderId(), $objOneMessage->getStrSenderId());
                $this->assertEquals($objMessage->getStrMessageRefId(), $objOneMessage->getStrMessageRefId());
            }

            $this->assertTrue($bitFound);
        }
    }

    public function testSendMessageObjectWithSendDateFuture()
    {
        $objSendDate = new Date();
        $objSendDate->setNextDay();
        $objMessage = $this->newMessage();
        $objUser = UserUser::getAllUsersByName("user")[0];

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage, $objUser, $objSendDate);

        $arrQueue = MessagingQueue::getObjectListFiltered();
        $this->assertEquals(1, count($arrQueue));

        $arrMessages = MessagingMessage::getMessagesByIdentifier($objMessage->getStrInternalIdentifier());
        $this->assertEquals(0, count($arrMessages));

        /** @var MessagingQueue $objQueue */
        $objQueue = $arrQueue[0];

        // the sender sets the date to the beginning of the day
        $objSendDate->setBeginningOfDay();

        $this->assertInstanceOf(MessagingQueue::class, $objQueue);
        $this->assertEquals($objUser->getSystemid(), $objQueue->getStrReceiver());
        $this->assertEquals($objSendDate->getLongTimestamp(), $objQueue->getObjSendDate()->getLongTimestamp());

        $objDbMessage = $objQueue->getMessage();

        $this->assertInstanceOf(MessagingMessage::class, $objDbMessage);
        $this->assertEquals($objMessage->getStrTitle(), $objDbMessage->getStrTitle());
        $this->assertEquals($objMessage->getStrBody(), $objDbMessage->getStrBody());
        $this->assertEquals($objMessage->getStrInternalIdentifier(), $objDbMessage->getStrInternalIdentifier());
        $this->assertEquals($objMessage->getStrMessageProvider(), $objDbMessage->getStrMessageProvider());
        $this->assertEquals($objMessage->getStrSenderId(), $objDbMessage->getStrSenderId());
        $this->assertEquals($objMessage->getStrMessageRefId(), $objDbMessage->getStrMessageRefId());
    }

    public function testSendMessageObjectWithSendDateNow()
    {
        $objSendDate = new Date();
        $objMessage = $this->newMessage();
        $objUser = UserUser::getAllUsersByName("user")[0];

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage, $objUser, $objSendDate);

        $arrQueue = MessagingQueue::getObjectListFiltered();
        $this->assertEquals(0, count($arrQueue));

        $arrMessages = MessagingMessage::getMessagesByIdentifier($objMessage->getStrInternalIdentifier());
        $this->assertEquals(1, count($arrMessages));

        /** @var MessagingQueue $objQueue */
        $objDbMessage = $arrMessages[0];

        $this->assertInstanceOf(MessagingMessage::class, $objDbMessage);
        $this->assertEquals($objMessage->getStrTitle(), $objDbMessage->getStrTitle());
        $this->assertEquals($objMessage->getStrBody(), $objDbMessage->getStrBody());
        $this->assertEquals($objMessage->getStrInternalIdentifier(), $objDbMessage->getStrInternalIdentifier());
        $this->assertEquals($objMessage->getStrMessageProvider(), $objDbMessage->getStrMessageProvider());
        $this->assertEquals($objMessage->getStrSenderId(), $objDbMessage->getStrSenderId());
        $this->assertEquals($objMessage->getStrMessageRefId(), $objDbMessage->getStrMessageRefId());
    }

    public function testUnreadCount()
    {
        $strText = generateSystemid() . " autotest";

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessage($strText, new UserGroup(SystemSetting::getConfigValue("_admins_group_id_")), new MessageproviderExceptions());

        $objGroup = new UserGroup(SystemSetting::getConfigValue("_admins_group_id_"));
        $arrUsers = $objGroup->getObjSourceGroup()->getUserIdsForGroup();
        $strUserId = $arrUsers[0];

        $arrMessages = MessagingMessage::getObjectListFiltered(null, $strUserId);
        $intUnread = MessagingMessage::getNumberOfMessagesForUser($strUserId, true);

        $this->assertEquals(1, $intUnread);
        $this->flushDBCache();

        $bitFound = false;
        foreach ($arrMessages as $objOneMessage) {
            $bitFound = true;
            $objOneMessage->setBitRead(true);
            $objOneMessage->updateObjectToDb();

            $this->assertEquals(0, MessagingMessage::getNumberOfMessagesForUser($strUserId, true));
        }

        $this->assertTrue($bitFound);
    }

    private function newMessage()
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

        return $objMessage;
    }

    private function removeAllQueueEntries()
    {
        $strPrefix = _dbprefix_;
        Database::getInstance()->_pQuery("DELETE FROM {$strPrefix}messages WHERE 1=1", []);
        Database::getInstance()->_pQuery("DELETE FROM {$strPrefix}messages_queue WHERE 1=1", []);
    }
}

