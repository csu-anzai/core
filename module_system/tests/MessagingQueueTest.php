<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Database;
use Kajona\System\System\Date;
use Kajona\System\System\Messageproviders\MessageproviderExceptions;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\MessagingMessagehandler;
use Kajona\System\System\MessagingQueue;
use Kajona\System\System\UserUser;

class MessagingQueueTest extends Testbase
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

    public function testSendMessageToQueue()
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
        $objMessageHandler->sendMessageToQueue($objMessage, $objUser, $objSendDate);

        $arrQueue = MessagingQueue::getObjectListFiltered();

        $this->assertEquals(1, count($arrQueue));

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

    public function testSendMessageToQueueSendNow()
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

        $objMessageHandler = $this->getMockBuilder(MessagingMessagehandler::class)
            ->setMethods(["sendMessageObject"])
            ->getMock();

        $objMessageHandler->expects($this->once())
            ->method("sendMessageObject");

        $objMessageHandler->sendMessageToQueue($objMessage, $objUser, $objSendDate);
    }

    public function testSendMessageToQueueSendFuture()
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

        $objMessageHandler = $this->getMockBuilder(MessagingMessagehandler::class)
            ->setMethods(["sendMessageObject"])
            ->getMock();

        $objMessageHandler->expects($this->never())
            ->method("sendMessageObject");

        $objMessageHandler->sendMessageToQueue($objMessage, $objUser, $objSendDate);
    }

    private function removeAllQueueEntries()
    {
        $strPrefix = _dbprefix_;
        Database::getInstance()->_pQuery("DELETE FROM {$strPrefix}messages_queue WHERE 1=1", []);
    }
}

