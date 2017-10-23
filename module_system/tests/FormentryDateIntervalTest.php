<?php

namespace Kajona\System\Tests;

use Kajona\System\Admin\Formentries\FormentryDateInterval;
use Kajona\System\System\Lang;

/**
 * FormentryDateIntervalTest
 */
class FormentryDateIntervalTest extends Testbase
{
    protected $strLang;

    public function setUp()
    {
        parent::setUp();

        $this->strLang = Lang::getInstance()->getStrTextLanguage();
        Lang::getInstance()->setStrTextLanguage("de");
    }

    public function tearDown()
    {
        parent::tearDown();

        Lang::getInstance()->setStrTextLanguage($this->strLang);
    }

    /**
     * @dataProvider getValueAsTextProvider
     */
    public function testGetValueAsText($strActual, $strExpect)
    {
        $objField = new FormentryDateInterval("", "");
        $objField->setStrValue($strActual);

        $this->assertEquals($strExpect, $objField->getValueAsText());
    }

    public function getValueAsTextProvider()
    {
        return [
            ["", "-"],
            ["P1D", "1 Tag"],
            ["P4D", "4 Tage"],
            ["P7D", "1 Woche"],
            ["P8D", "8 Tage"],
            ["P14D", "2 Wochen"],
            ["P1W", "1 Woche"],
            ["P4W", "4 Wochen"],
            ["P1M", "1 Monat"],
            ["P4M", "4 Monate"],
            ["P1Y", "1 Jahr"],
            ["P4Y", "4 Jahre"],
        ];
    }
}
