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
        $objAdmin = $this->getAdminMock("GET");

        Carrier::getInstance()->setParam("class", MessagingMessage::class);

        $strResult = $objAdmin->action("dispatch");

        // we must remove several date values
        $strResult = preg_replace("|[a-f0-9]{20}|ims", "", $strResult);
        $strResult = preg_replace("|[0-9]{2}\\\\/[0-9]{2}\\\\/[0-9]{4}|ims", "", $strResult);
        $strResult = preg_replace("|[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}|ims", "", $strResult);

        $strExpect = <<<'JSON'
{
    "totalCount": "2",
    "startIndex": 0,
    "filter": null,
    "entries": [
        {
            "_id": "",
            "_class": "Kajona\\System\\System\\MessagingMessage",
            "_icon": "icon_mail",
            "_displayName": "unittest demo message",
            "_additionalInfo": "message additional info",
            "_longDescription": "S: ",
            "strTitle": "unittest demo message 1",
            "strBody": "unittest demo message body 1"
        },
        {
            "_id": "",
            "_class": "Kajona\\System\\System\\MessagingMessage",
            "_icon": "icon_mail",
            "_displayName": "unittest demo message",
            "_additionalInfo": "message additional info",
            "_longDescription": "S: ",
            "strTitle": "unittest demo message 2",
            "strBody": "unittest demo message body 2"
        },
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString($strExpect, $strResult, $strResult);
    }

    public function testGetNoClass()
    {
        Carrier::getInstance()->setParam("class", "");
        Carrier::getInstance()->setParam("message_title", "");
        Carrier::getInstance()->setParam("message_datestart", "");

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
        Carrier::getInstance()->setParam("message_title", "");
        Carrier::getInstance()->setParam("message_body", "");

        $arrData = array(
            "message_title" => "lorem ipsum",
            "message_body" => date('m/d/Y'),
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
        $arrUsers = UserUser::getAllUsersByName("admin");
        Session::getInstance()->loginUser($arrUsers[0]);

        Carrier::getInstance()->setParam("class", MessagingMessage::class);
        Carrier::getInstance()->setParam("message_title", "");
        Carrier::getInstance()->setParam("message_body", "");

        $arrData = array(
            "bar" => "foo"
        );

        $objAdmin = $this->getAdminMock("POST", $arrData);

        $strResult = $objAdmin->action("dispatch");
        $strExpect = <<<JSON
{
    "success": false,
    "errors": {
        "message_title": [
            "'Title' is empty"
        ],
        "message_body": [
            "'Body' is empty"
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
