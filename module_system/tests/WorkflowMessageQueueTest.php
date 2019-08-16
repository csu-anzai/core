<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Database;
use Kajona\System\System\Date;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Messageproviders\MessageproviderExceptions;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\MessagingMessagehandler;
use Kajona\System\System\MessagingQueue;
use Kajona\System\System\Objectfactory;
use Kajona\System\System\UserUser;
use Kajona\System\System\Workflows\WorkflowMessageQueue;

class WorkflowMessageQueueTest extends Testbase
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
        $objMessage1 = $this->newMessage();
        $objSendDate1 = new Date();
        $objSendDate1->setNextDay();

        $objMessage2 = $this->newMessage();
        $objSendDate2 = new Date();
        $objSendDate2->setNextDay();
        $objSendDate2->setNextDay();

        $objUser = new UserUser();
        $objUser->setStrUsername(generateSystemid());
        ServiceLifeCycleFactory::getLifeCycle(get_class($objUser))->update($objUser);

        $objMessageHandler = new MessagingMessagehandler();
        $objMessageHandler->sendMessageObject($objMessage1, $objUser, $objSendDate1);
        $objMessageHandler->sendMessageObject($objMessage2, $objUser, $objSendDate2);

        $arrQueue = MessagingQueue::getObjectListFiltered();
        $this->assertEquals(2, count($arrQueue));

        $objWorkflow = $this->getMockBuilder(WorkflowMessageQueue::class)
            ->setMethods(["getNowDate"])
            ->getMock();

        $objWorkflow->expects($this->once())
            ->method("getNowDate")
            ->willReturn($objSendDate1);

        $objWorkflow->execute();

        $arrQueue = MessagingQueue::getObjectListFiltered();
        $this->assertEquals(1, count($arrQueue));

        $arrMessages = MessagingMessage::getMessagesByIdentifier($objMessage1->getStrInternalIdentifier());
        $this->assertEquals(1, count($arrMessages));

        $arrMessages = MessagingMessage::getMessagesByIdentifier($objMessage2->getStrInternalIdentifier());
        $this->assertEquals(0, count($arrMessages));

        $objUser->deleteObjectFromDatabase();
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
        $objDb = Database::getInstance();

        $arrResult = $objDb->getPArray("SELECT queue_id FROM agp_messages_queue", []);
        foreach ($arrResult as $arrRow) {
            $objQueue = Objectfactory::getInstance()->getObject($arrRow["queue_id"]);
            $objQueue->deleteObjectFromDatabase();
        }
    }
}


