<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Date;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\MessagingAlert;
use Kajona\System\System\MessagingAlertLifeCycle;
use Kajona\System\System\MessagingMessagehandler;
use Kajona\System\System\UserUser;

class MessagingMessagehandlerTest extends Testbase
{
    public function testSendAlertToUser()
    {
        $objAlert = new MessagingAlert();
        $objUser = new UserUser();
        $objUser->setSystemid(generateSystemid());

        $objLifeCycle = $this->getMockBuilder(MessagingAlertLifeCycle::class)
            ->setMethods(["update"])
            ->getMock();

        $objLifeCycle->expects($this->once())
            ->method("update")
            ->with($this->equalTo($objAlert));

        $objLifeCycleFactory = $this->getMockBuilder(ServiceLifeCycleFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(["factory"])
            ->getMock();

        $objLifeCycleFactory->expects($this->once())
            ->method("factory")
            ->willReturn($objLifeCycle);

        $objHandler = new MessagingMessagehandler($objLifeCycleFactory);
        $objHandler->sendAlertToUser($objAlert, $objUser);

        $this->assertEquals($objAlert->getStrUser(), $objUser->getSystemid());
        $this->assertInstanceOf(Date::class, $objAlert->getObjSendDate());
    }

    public function testSendAlertToUserInActiveUser()
    {
        $objAlert = new MessagingAlert();
        $objUser = new UserUser();
        $objUser->setSystemid(generateSystemid());
        $objUser->setIntRecordStatus(0);

        $objLifeCycle = $this->getMockBuilder(MessagingAlertLifeCycle::class)
            ->setMethods(["update"])
            ->getMock();

        $objLifeCycle->expects($this->never())
            ->method("update")
            ->with($this->equalTo($objAlert));

        $objLifeCycleFactory = $this->getMockBuilder(ServiceLifeCycleFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(["factory"])
            ->getMock();

        $objLifeCycleFactory->expects($this->never())
            ->method("factory")
            ->willReturn($objLifeCycle);

        $objHandler = new MessagingMessagehandler($objLifeCycleFactory);
        $objHandler->sendAlertToUser($objAlert, $objUser);

        $this->assertEmpty($objAlert->getStrUser());
        $this->assertEmpty($objAlert->getObjSendDate());
    }
}

