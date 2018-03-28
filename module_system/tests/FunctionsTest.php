<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Carrier;
use Kajona\System\System\Date;

/**
 * Class class_test_functions
 */
class FunctionsTest extends Testbase
{

    /**
     * @return void
     */
    public function testReplaceTextLinks()
    {
        //change nothing
        $this->assertEquals("hello world", replaceTextLinks("hello world"));

        //simple link
        $this->assertEquals("hello <a href=\"http://www.kajona.de\">http://www.kajona.de</a> world", replaceTextLinks("hello http://www.kajona.de world"));
        $this->assertEquals("<a href=\"http://www.kajona.de\">http://www.kajona.de</a> world", replaceTextLinks("http://www.kajona.de world"));
        $this->assertEquals("hello <a href=\"http://www.kajona.de\">http://www.kajona.de</a>", replaceTextLinks("hello http://www.kajona.de"));
        $this->assertEquals("hello <a href=\"https://www.kajona.de\">https://www.kajona.de</a> world", replaceTextLinks("hello https://www.kajona.de world"));
        $this->assertEquals("hello <a href=\"ftp://www.kajona.de\">ftp://www.kajona.de</a> world", replaceTextLinks("hello ftp://www.kajona.de world"));

        $this->assertEquals("hello <a href=\"ftp://www.kajona.de\">ftp://www.kajona.de</a> world hello <a href=\"ftp://www.kajona.de\">ftp://www.kajona.de</a> world", replaceTextLinks("hello ftp://www.kajona.de world hello ftp://www.kajona.de world"));

        //no replacement if protocol is missing
        $this->assertEquals("hello www.kajona.de world", replaceTextLinks("hello www.kajona.de world"));

        //keep links already existing
        $this->assertEquals("hello <a href=\"http://www.kajona.de\">aaaa</a> world", replaceTextLinks("hello <a href=\"http://www.kajona.de\">aaaa</a> world"));
        $this->assertEquals("hello <a href=\"http://www.kajona.de\">http://www.kajona.de</a> world", replaceTextLinks("hello <a href=\"http://www.kajona.de\">http://www.kajona.de</a> world"));

        $strText = <<<TEXT
Die Risikoanalyse 'Risikoanalyse Rahmenvertrag (28.03.2018)' wurde von Status 'In Bearbeitung' nach 'In Review' gesetzt.

Die Zusammenfassung können Sie unter folgendem Link einsehen:
https://aquarium.kajona.de:8443/master/#/riskanalysis/showSummary/08fc1be5abb56446ea4b

Bitte verwenden Sie den unten stehenden Link, um die einzelnen Risiken einzusehen:
https://aquarium.kajona.de:8443/master/#/riskanalysis/riskWizardPage/08fc1be5abb56446ea4b

https://aquarium.kajona.de:8443/master/#/riskanalysis/riskWizardPage/08fc1be5abb56446ea4b<br />

Unter dem folgendem Link können Sie die aktuelle Risikoanalyse vergleichen:
https://aquarium.kajona.de:8443/master/#/riskanalysis/showDiff?left_container_id=08fc1be5abb56446ea4b&right_container_id=08fc1be5abb56446ea4b
TEXT;

        $strExpect = <<<TEXT
Die Risikoanalyse 'Risikoanalyse Rahmenvertrag (28.03.2018)' wurde von Status 'In Bearbeitung' nach 'In Review' gesetzt.

Die Zusammenfassung können Sie unter folgendem Link einsehen:
<a href="https://aquarium.kajona.de:8443/master/#/riskanalysis/showSummary/08fc1be5abb56446ea4b">https://aquarium.kajona.de:8443/master/#/riskanalysis/showSummary/08fc1be5abb56446ea4b</a>

Bitte verwenden Sie den unten stehenden Link, um die einzelnen Risiken einzusehen:
<a href="https://aquarium.kajona.de:8443/master/#/riskanalysis/riskWizardPage/08fc1be5abb56446ea4b">https://aquarium.kajona.de:8443/master/#/riskanalysis/riskWizardPage/08fc1be5abb56446ea4b</a>

<a href="https://aquarium.kajona.de:8443/master/#/riskanalysis/riskWizardPage/08fc1be5abb56446ea4b">https://aquarium.kajona.de:8443/master/#/riskanalysis/riskWizardPage/08fc1be5abb56446ea4b</a><br />

Unter dem folgendem Link können Sie die aktuelle Risikoanalyse vergleichen:
<a href="https://aquarium.kajona.de:8443/master/#/riskanalysis/showDiff?left_container_id=08fc1be5abb56446ea4b&right_container_id=08fc1be5abb56446ea4b">https://aquarium.kajona.de:8443/master/#/riskanalysis/showDiff?left_container_id=08fc1be5abb56446ea4b&right_container_id=08fc1be5abb56446ea4b</a>
TEXT;

        $strActual = replaceTextLinks($strText);
        $strActual = str_replace(["\r\n", "\n", "\r"], "\n", $strActual);
        $strExpect = str_replace(["\r\n", "\n", "\r"], "\n", $strExpect);

        $this->assertEquals($strExpect, $strActual);
    }

    /**
     * @return void
     */
    public function testDateToString()
    {

        Carrier::getInstance()->getObjLang()->setStrTextLanguage("de");
        if (Carrier::getInstance()->getObjLang()->getLang("dateStyleShort", "system") != "d.m.Y") {
            return;
        }

        $this->assertEquals("15.05.2013", dateToString(new Date(20130515122324), false));
        $this->assertEquals("15.05.2013 12:23:24", dateToString(new Date(20130515122324), true));

        $this->assertEquals("15.05.2013", dateToString(new Date("20130515122324"), false));
        $this->assertEquals("15.05.2013 12:23:24", dateToString(new Date("20130515122324"), true));

        $this->assertEquals("15.05.2013", dateToString(20130515122324, false));
        $this->assertEquals("15.05.2013 12:23:24", dateToString(20130515122324, true));

        $this->assertEquals("15.05.2013", dateToString("20130515122324", false));
        $this->assertEquals("15.05.2013 12:23:24", dateToString("20130515122324", true));


        $this->assertEquals("", dateToString(null));
        $this->assertEquals("", dateToString(""));
        $this->assertEquals("", dateToString("asdfsfdsfdsfds"));


        Carrier::getInstance()->getObjLang()->setStrTextLanguage("en");
        if (Carrier::getInstance()->getObjLang()->getLang("dateStyleShort", "system") != "m/d/Y") {
            return;
        }

        $this->assertEquals("05/15/2013", dateToString(new Date(20130515122324), false));
        $this->assertEquals("05/15/2013 12:23:24", dateToString(new Date(20130515122324), true));

        $this->assertEquals("05/15/2013", dateToString(new Date("20130515122324"), false));
        $this->assertEquals("05/15/2013 12:23:24", dateToString(new Date("20130515122324"), true));

        $this->assertEquals("05/15/2013", dateToString(20130515122324, false));
        $this->assertEquals("05/15/2013 12:23:24", dateToString(20130515122324, true));

        $this->assertEquals("05/15/2013", dateToString("20130515122324", false));
        $this->assertEquals("05/15/2013 12:23:24", dateToString("20130515122324", true));
    }


    public function testValidateSystemid()
    {
        $this->assertTrue(validateSystemid("12345678901234567890"));
        $this->assertTrue(validateSystemid("abcdefghijklmnopqrst"));

        $this->assertTrue(!validateSystemid("123456789012345678901"));
        $this->assertTrue(!validateSystemid("abcdefghijklmnopqrstu"));

        $this->assertTrue(!validateSystemid("1234567890123456789"));
        $this->assertTrue(!validateSystemid("abcdefghijklmnopqrs"));

        $this->assertTrue(!validateSystemid("12345678901234567890 123"));
        $this->assertTrue(!validateSystemid("abcdefghijklmnopqrst abc"));

        $this->assertTrue(!validateSystemid("abc 12345678901234567890 123"));
        $this->assertTrue(!validateSystemid("123 abcdefghijklmnopqrst abc"));

        $this->assertTrue(!validateSystemid("1234567890!234567890"));
        $this->assertTrue(!validateSystemid("abcdefghij!lmnopqrst"));

        $this->assertTrue(!validateSystemid("1234567890 234567890"));
        $this->assertTrue(!validateSystemid("abcdefghij lmnopqrst"));
    }

    public function testSysIdValidationPerformanceTest()
    {

        $strTest = "1234567890AbCdEfghij";


        $intStart = microtime(true);
        for ($intI = 0; $intI < 10000; $intI++) {
            $this->assertTrue(strlen($strTest) == 20 && preg_match("/([a-z|A-a|0-9]){20}/", $strTest));
        }
        $intEnd = microtime(true);
        //echo "preg based : " . ($intEnd - $intStart) . " sec\n";

        $intStart = microtime(true);

        for ($intI = 0; $intI < 10000; $intI++) {
            $this->assertTrue(strlen($strTest) == 20 && ctype_alnum($strTest));
        }
        $intEnd = microtime(true);
        //echo "ctype based : " . ($intEnd - $intStart) . " sec\n";
    }
}

