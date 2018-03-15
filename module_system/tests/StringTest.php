<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Date;
use Kajona\System\System\StringUtil;

class StringTest extends Testbase
{
    /**
     * @dataProvider equalsProvider
     */
    public function testEquals($strLeft, $strRight)
    {
        $this->assertTrue(StringUtil::equals($strLeft, $strRight));
    }

    public function equalsProvider()
    {
        return [
            ["foo", "foo"],
            ["tèst", "tèst"],
        ];
    }

    /**
     * @dataProvider equalsProviderFalse
     */
    public function testEqualsFalse($strLeft, $strRight)
    {
        $this->assertFalse(StringUtil::equals($strLeft, $strRight));
    }

    public function equalsProviderFalse()
    {
        return [
            ["test", "tèst"],
            ["tèst", "tést"],
        ];
    }

    /**
     * @dataProvider equalsLength
     */
    public function testLength($intExpect, $strString)
    {
        $this->assertEquals($intExpect, StringUtil::length($strString));
    }

    public function equalsLength()
    {
        return [
            [4, "tèst"],
            [4, "test"],
        ];
    }

    public function testStrToDate()
    {
        $strString = "";
        $objResult = StringUtil::toDate($strString);
        $this->assertNull($objResult);

        $strString = "0";
        $objResult = StringUtil::toDate($strString);
        $this->assertTrue($objResult instanceof Date);

        $strString = new Date();
        $objResult = StringUtil::toDate($strString);
        $this->assertTrue($objResult instanceof Date);
    }

    public function testStrToInt()
    {
        $strString = "";
        $intResult = StringUtil::toInt($strString);
        $this->assertNull($intResult);

        $strString = 0;
        $intResult = StringUtil::toInt($strString);
        $this->assertEquals(0, $intResult);

        $strString = "0";
        $intResult = StringUtil::toInt($strString);
        $this->assertEquals(0, $intResult);

        $strString = "-42";
        $intResult = StringUtil::toInt($strString);
        $this->assertEquals(-42, $intResult);
    }

    public function testStrToArray()
    {

        //empty string
        $strString = "";
        $arrResult = StringUtil::toArray($strString);
        $this->assertNull($arrResult);

        //null value
        $strString = null;
        $arrResult = StringUtil::toArray($strString);
        $this->assertNull($arrResult);

        //empty string
        $strString = "null";
        $arrResult = StringUtil::toArray($strString);
        $this->assertCount(1, $arrResult);
        $this->assertEquals("null", $arrResult[0]);

        //standard cal with string
        $strString = "1,0,3";
        $arrResult = StringUtil::toArray($strString);
        $this->assertTrue(is_array($arrResult));
        $this->assertCount(3, $arrResult);

        //Empty delimiter
        $strString = "1,0,3";
        $arrResult = StringUtil::toArray($strString, "");
        $this->assertNull($arrResult);

        //Delimiter "."
        $strString = "1.0.3";
        $arrResult = StringUtil::toArray($strString, ".");
        $this->assertTrue(is_array($arrResult));
        $this->assertCount(3, $arrResult);

        $strString = array();
        $arrResult = StringUtil::toArray($strString);
        $this->assertTrue(is_array($arrResult));
        $this->assertCount(0, $arrResult);

        $strString = array(2,3,4);
        $arrResult = StringUtil::toArray($strString);
        $this->assertTrue(is_array($arrResult));
        $this->assertCount(3, $arrResult);
        $this->assertEquals(2, $arrResult[0]);
        $this->assertEquals(3, $arrResult[1]);
        $this->assertEquals(4, $arrResult[2]);
    }

    /**
     * @dataProvider parseUrlStringProvider
     */
    public function testParseUrlString($strString)
    {
        $arrExpected = [];
        parse_str($strString, $arrExpected);
        $arrResult = StringUtil::parseUrlString($strString);
        $this->assertEquals($arrExpected, $arrResult);
    }

    public function parseUrlStringProvider()
    {
        return array(
            ["first=value&second=value"],
            ["first=value&second=value&redirect=".urlencode("/#avc/katze")],
            ["first=value&second=value#acbg"],
            ["first=value&arr[]=foo+bar&arr[]=baz"],
            ["first=value&arr[2]=foo+bar&arr[3]=baz"],
            ["action=search&interest[0]=sports&interest[1]=music&sort=interest"],
        );
    }

    /**
     * @dataProvider startsWithProvider()
     */
    public function testStartsWith($intExpectedResult, $strString, $strSearch)
    {
        $result = StringUtil::startsWith($strString, $strSearch);
        $this->assertEquals($intExpectedResult, $result);
    }

    /**
     * @dataProvider endsWithProvider()
     */
    public function testEndsWith($intExpectedResult, $strString, $strSearch)
    {
        $result = StringUtil::endsWith($strString, $strSearch);
        $this->assertEquals($intExpectedResult, $result);
    }

    /**
     * @dataProvider jsSafeStringProvider
     */
    public function testJsSafeString($strString, $strExpect)
    {
        $this->assertSame($strExpect, StringUtil::jsSafeString($strString));
    }

    public function jsSafeStringProvider()
    {
        return array(
            array('foobar', 'foobar'),
            array('foo<bar', 'foo&lt;bar'),
            array('foo"bar', 'foo\&quot;bar'),
            array('foo\'bar', 'foo\&#039;bar'),
            array('foo' . "\n" . 'bar', 'foo\nbar'),
        );
    }

    public function startsWithProvider()
    {
        return array(
            array(true, '123 456', '123'),
            array(true, '123 456', '123 456'),
            array(true, '123 456', '123 '),
            array(true, '%&§$%/()', '%&'),
            array(false, '123 456', '456'),
            array(false, '123 456', '123 4567'),
            array(false, 'ABC', 'abc'),
        );
    }

    public function endsWithProvider()
    {
        return array(
            array(true, '123 456', '456'),
            array(true, '123 456', '123 456'),
            array(true, '123 456', ' 456'),
            array(true, '%&§$%/()', '()'),
            array(false, '123 456', '123'),
            array(false, '123 456', '123 4567'),
            array(false, 'ABC', 'abc'),
        );
    }
}

