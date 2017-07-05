<?php

namespace Kajona\Jsonapi\Tests;

use Kajona\Jsonapi\Admin\JsonapiAdmin;
use Kajona\System\System\Carrier;
use Kajona\System\System\MessagingMessage;
use Kajona\System\System\ResponseObject;
use Kajona\System\System\Session;
use Kajona\System\System\UserUser;
use PHPUnit\Framework\TestCase;

class JsonapiAdminTest extends TestCase
{



    public function testGet()
    {
        $this->markAsRisky();
        $this->markTestSkipped("Needs to be refactored");

        $arrUsers = UserUser::getAllUsersByName("admin");

        $objMessage = new MessagingMessage();
        $objMessage->setStrUser($arrUsers[0]->getStrSystemid());
        $objMessage->setStrTitle("unittest demo message title 1");
        $objMessage->setStrBody("unittest demo message body 1");
        $objMessage->updateObjectToDb();

        $strSystemid = $objMessage->getStrSystemid();
        $strAdditionalInfo = $objMessage->getStrAdditionalInfo();
        $strDisplayName = $objMessage->getStrDisplayName();


        $objAdmin = $this->getAdminMock("GET");

        Carrier::getInstance()->setParam("class", MessagingMessage::class);

        $strResult = $objAdmin->action("dispatch");

        // we must remove several date values
        $strResult = preg_replace("|[a-f0-9]{20}|ims", "", $strResult);
        $strResult = preg_replace("|[0-9]{2}\\\\/[0-9]{2}\\\\/[0-9]{4}|ims", "", $strResult);
        $strResult = preg_replace("|[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}|ims", "", $strResult);

        $strExpect = <<<"JSON"
{
    "totalCount": "23",
    "startIndex": 0,
    "filter": null,
    "entries": [
        {
            "_id": "{$strSystemid}",
            "_class": "Kajona\\\\System\\\\System\\\\MessagingMessage",
            "_icon": "icon_mailNew",
            "_displayName": "{$strDisplayName}",
            "_additionalInfo": "{$strAdditionalInfo}",
            "_longDescription": "",
            "strTitle": "unittest demo message title 1",
            "strBody": "unittest demo message body 1",
            "bitRead": "false"
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString($strExpect, $strResult, "json different");

        $objMessage->deleteObjectFromDatabase();
    }

    public function testGetNoClass()
    {
        Carrier::getInstance()->setParam("class", "");

        $objAdmin = $this->getAdminMock("GET");

        $strResult = $objAdmin->action("dispatch");
        $strExpect = <<<JSON
{
  "success": false,
  "message": "Invalid class name"
}
JSON;

        $this->assertEquals('HTTP/1.1 400 Bad Request', ResponseObject::getInstance()->getStrStatusCode(), $strResult);
        $this->assertJsonStringEqualsJsonString($strExpect, $strResult, $strResult);
    }

    public function testPost()
    {
        $arrUsers = UserUser::getAllUsersByName("admin");
        Session::getInstance()->loginUser($arrUsers[0]);

        Carrier::getInstance()->setParam("class", MessagingMessage::class);

        $arrData = array(
            "messaging_user" => "admin",
            "messaging_user_id" => $arrUsers[0]->getStrSystemid(),
            "messaging_title" => "test title",
            "messaging_body" => "test body",
        );

        $objAdmin = $this->getAdminMock("POST", $arrData);

        $strResult = $objAdmin->action("dispatch");
        $strExpect = <<<JSON
{
    "success": true,
    "message": "Create entry successful"
}
JSON;

        Session::getInstance()->logout();

        $this->assertEquals('HTTP/1.1 200 OK', ResponseObject::getInstance()->getStrStatusCode(), $strResult);
        $this->assertJsonStringEqualsJsonString($strExpect, $strResult, $strResult);
    }

    public function testPostInvalidData()
    {
        $this->markAsRisky();
        $this->markTestSkipped("Needs to be refactored");

        $arrUsers = UserUser::getAllUsersByName("admin");
        Session::getInstance()->loginUser($arrUsers[0]);

        Carrier::getInstance()->setParam("class", MessagingMessage::class);

        $arrData = array(
            "bar" => "foo"
        );

        $objAdmin = $this->getAdminMock("POST", $arrData);

        $strResult = $objAdmin->action("dispatch");
        $strExpect = <<<JSON
{
    "success": false,
    "errors": {
        "messaging_user": [
            "'An' ist leer"
        ],
        "messaging_title": [
            "'Betreff' ist leer"
        ],
        "messaging_body": [
            "'Nachricht' ist leer"
        ]
    }
}
JSON;

        Session::getInstance()->logout();

        $this->assertEquals('HTTP/1.1 400 Bad Request', ResponseObject::getInstance()->getStrStatusCode(), $strResult);
        $this->assertJsonStringEqualsJsonString($strExpect, $strResult, $strResult);
    }

    /**
     * @param string $strMethod
     * @param array|null $strBody
     * @return JsonapiAdmin
     */
    protected function getAdminMock($strMethod, array $strBody = null)
    {
        $objAdmin = $this->getMockBuilder(JsonapiAdmin::class)
            ->setMethods(array("getRequestMethod", "getRawInput"))
            ->getMock();

        $objAdmin->setArrModuleEntry("modul", "jsonapi");
        $objAdmin->setArrModuleEntry("module", "jsonapi");
        $objAdmin->setArrModuleEntry("moduleId", _jsonapi_module_id_);

        $objAdmin->expects($this->once())
            ->method("getRequestMethod")
            ->will($this->returnValue(strtolower($strMethod)));

        $objAdmin->expects($this->any())
            ->method("getRawInput")
            ->will($this->returnValue($strBody ? json_encode($strBody) : ""));

        return $objAdmin;
    }
}
