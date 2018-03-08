<?php

namespace Kajona\System\Tests;

use Kajona\System\System\Date;
use Kajona\System\System\DatePeriodEnum;
use Kajona\System\System\DateRange;

class DateRangeTest extends Testbase
{
    public function testGetDateRangeDay()
    {
        // without hours
        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160104000000), DatePeriodEnum::DAY());
        $this->assertEquals(3, count($arrRanges));
        $this->assertEquals(20160101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160101235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160102000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160102235959, $arrRanges[1][1]->getLongTimestamp());
        $this->assertEquals(20160103000000, $arrRanges[2][0]->getLongTimestamp());
        $this->assertEquals(20160103235959, $arrRanges[2][1]->getLongTimestamp());

        // with hours
        $arrRanges = DateRange::getDateRange(new Date(20160101140000), new Date(20160104140000), DatePeriodEnum::DAY());
        $this->assertEquals(3, count($arrRanges));
        $this->assertEquals(20160101140000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160102135959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160102140000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160103135959, $arrRanges[1][1]->getLongTimestamp());
        $this->assertEquals(20160103140000, $arrRanges[2][0]->getLongTimestamp());
        $this->assertEquals(20160104135959, $arrRanges[2][1]->getLongTimestamp());

        // with end date hours > start date hours
        $arrRanges = DateRange::getDateRange(new Date(20160101140000), new Date(20160104180000), DatePeriodEnum::DAY());
        $this->assertEquals(3, count($arrRanges));
        $this->assertEquals(20160101140000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160102135959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160102140000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160103135959, $arrRanges[1][1]->getLongTimestamp());
        $this->assertEquals(20160103140000, $arrRanges[2][0]->getLongTimestamp());
        $this->assertEquals(20160104135959, $arrRanges[2][1]->getLongTimestamp());

        // with end date hours < start date hours
        $arrRanges = DateRange::getDateRange(new Date(20160101140000), new Date(20160104120000), DatePeriodEnum::DAY());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20160101140000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160102135959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160102140000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160103135959, $arrRanges[1][1]->getLongTimestamp());
    }

    public function testGetDateRangeWeek()
    {
        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160106000000), DatePeriodEnum::WEEK());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160107000000), DatePeriodEnum::WEEK());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160108000000), DatePeriodEnum::WEEK());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160107235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160109000000), DatePeriodEnum::WEEK());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160107235959, $arrRanges[0][1]->getLongTimestamp());
    }

    public function testGetDateRangeMonth()
    {
        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160130000000), DatePeriodEnum::MONTH());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160131000000), DatePeriodEnum::MONTH());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160201000000), DatePeriodEnum::MONTH());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160131235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20160301000000), DatePeriodEnum::MONTH());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160131000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160228235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRange(new Date(20151201000000), new Date(20160101000000), DatePeriodEnum::MONTH());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20151201000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20151231235959, $arrRanges[0][1]->getLongTimestamp());
    }

    public function testGetDateRangeYear()
    {
        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160130000000), DatePeriodEnum::YEAR());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160131000000), DatePeriodEnum::YEAR());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160301000000), DatePeriodEnum::YEAR());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20160131000000), DatePeriodEnum::YEAR());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20170201000000), DatePeriodEnum::YEAR());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160131000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20170130235959, $arrRanges[0][1]->getLongTimestamp());
    }

    public function testGetDateRangeYear2()
    {
        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160130000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160131000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160301000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20160131000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20170201000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20180131000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160131000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20180130235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20180201000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160131000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20180130235959, $arrRanges[0][1]->getLongTimestamp());
    }

    public function testGetDateRangeYear3()
    {
        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160130000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160131000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160101000000), new Date(20160301000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20160131000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20170201000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(0, count($arrRanges));

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20190131000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160131000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20190130235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRange(new Date(20160131000000), new Date(20190201000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160131000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20190130235959, $arrRanges[0][1]->getLongTimestamp());
    }

    public function testGetDateRangeCompleteWeek()
    {
        $arrRanges = DateRange::getDateRangeComplete(new Date(20160706000000), new Date(20160715000000), DatePeriodEnum::WEEK());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20160704000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160710235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160711000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160717235959, $arrRanges[1][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20160704000000), new Date(20160710235959), DatePeriodEnum::WEEK());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160704000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160710235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20160704000000), new Date(20160711000000), DatePeriodEnum::WEEK());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20160704000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160710235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160711000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160717235959, $arrRanges[1][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20160706120000), new Date(20160715080000), DatePeriodEnum::WEEK());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20160704000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160710235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160711000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160717235959, $arrRanges[1][1]->getLongTimestamp());
    }

    public function testGetDateRangeCompleteMonth()
    {
        $arrRanges = DateRange::getDateRangeComplete(new Date(20160606000000), new Date(20160713000000), DatePeriodEnum::MONTH());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20160601000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160630235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160701000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160731235959, $arrRanges[1][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20160601000000), new Date(20160630235959), DatePeriodEnum::MONTH());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20160601000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160630235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20160601000000), new Date(20160701000000), DatePeriodEnum::MONTH());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20160601000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20160630235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160701000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20160731235959, $arrRanges[1][1]->getLongTimestamp());
    }

    public function testGetDateRangeCompleteYear()
    {
        $arrRanges = DateRange::getDateRangeComplete(new Date(20150606000000), new Date(20160713000000), DatePeriodEnum::YEAR());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20151231235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160101000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20161231235959, $arrRanges[1][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20151231235959), DatePeriodEnum::YEAR());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20151231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20160101000000), DatePeriodEnum::YEAR());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20151231235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20160101000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20161231235959, $arrRanges[1][1]->getLongTimestamp());


        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20150201000000), DatePeriodEnum::YEAR());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20151231235959, $arrRanges[0][1]->getLongTimestamp());
    }

    public function testGetDateRangeCompleteYear2()
    {
        $arrRanges = DateRange::getDateRangeComplete(new Date(20150606000000), new Date(20160713000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20161231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20151231235959), DatePeriodEnum::YEAR2());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20161231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20160101000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20161231235959, $arrRanges[0][1]->getLongTimestamp());


        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20170101000000), DatePeriodEnum::YEAR2());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20161231235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20170101000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20181231235959, $arrRanges[1][1]->getLongTimestamp());
    }

    public function testGetDateRangeCompleteYear3()
    {
        $arrRanges = DateRange::getDateRangeComplete(new Date(20150606000000), new Date(20160713000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20171231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20151231235959), DatePeriodEnum::YEAR3());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20171231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20160101000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20171231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20170101000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(1, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20171231235959, $arrRanges[0][1]->getLongTimestamp());

        $arrRanges = DateRange::getDateRangeComplete(new Date(20150101000000), new Date(20180101000000), DatePeriodEnum::YEAR3());
        $this->assertEquals(2, count($arrRanges));
        $this->assertEquals(20150101000000, $arrRanges[0][0]->getLongTimestamp());
        $this->assertEquals(20171231235959, $arrRanges[0][1]->getLongTimestamp());
        $this->assertEquals(20180101000000, $arrRanges[1][0]->getLongTimestamp());
        $this->assertEquals(20201231235959, $arrRanges[1][1]->getLongTimestamp());
    }
}
