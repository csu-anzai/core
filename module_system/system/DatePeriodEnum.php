<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Enum for periods
 *
 * @package module_search
 * @author stefan.meyer1@yahoo.de.de
 * @since 4.7
 *
 * @method static DatePeriodEnum DAY()
 * @method static DatePeriodEnum WEEK()
 * @method static DatePeriodEnum MONTH()
 * @method static DatePeriodEnum QUARTER()
 * @method static DatePeriodEnum HALFYEAR()
 * @method static DatePeriodEnum YEAR()
 */
class DatePeriodEnum extends EnumBase
{
    private static $INT_FREQUENCY_DISABLED = 0;
    private static $INT_FREQUENCY_DAY = 1;
    private static $INT_FREQUENCY_WEEK = 2;
    private static $INT_FREQUENCY_MONTH = 3;
    private static $INT_FREQUENCY_QUARTER = 4;
    private static $INT_FREQUENCY_HALFYEAR = 5;
    private static $INT_FREQUENCY_YEAR = 6;


    /**
     * Return the array of possible, so allowed values for the current enum
     *
     * @return string[]
     */
    protected function getArrValues()
    {
        return array("DAY", "WEEK", "MONTH", "QUARTER", "HALFYEAR", "YEAR");
    }


    /**
     * Converts frequency integer to DatePeriodEnum
     *
     * Returns null for $INT_FREQUENCY_DISABLED
     *
     * @param $intFrequency
     * @return DatePeriodEnum|null
     */
    public static function convertFrequencyIntToEnum($intFrequency)
    {
        switch ($intFrequency) {
            case self::$INT_FREQUENCY_YEAR:
                return DatePeriodEnum::YEAR();
            case self::$INT_FREQUENCY_HALFYEAR;
                return DatePeriodEnum::HALFYEAR();
            case self::$INT_FREQUENCY_QUARTER:
                return DatePeriodEnum::QUARTER();
            case self::$INT_FREQUENCY_MONTH:
                return DatePeriodEnum::MONTH();
            case self::$INT_FREQUENCY_WEEK:
                return DatePeriodEnum::WEEK();
            case self::$INT_FREQUENCY_DAY:
                return DatePeriodEnum::DAY();
            case self::$INT_FREQUENCY_DISABLED:
                return null;
            default:
                return null;
        }
    }

    /**
     * Converts frequency DatePeriodEnum to integer
     *
     * @param DatePeriodEnum $enumFrequency
     * @return int|null
     */
    public static function convertFrequencyEnumToInt(DatePeriodEnum $enumFrequency)
    {
        if($enumFrequency->equals(DatePeriodEnum::YEAR())) {
            return self::$INT_FREQUENCY_YEAR;
        }
        elseif($enumFrequency->equals(DatePeriodEnum::HALFYEAR())) {
            return self::$INT_FREQUENCY_HALFYEAR;
        }
        elseif($enumFrequency->equals(DatePeriodEnum::QUARTER())) {
            return self::$INT_FREQUENCY_QUARTER;
        }
        elseif($enumFrequency->equals(DatePeriodEnum::MONTH())) {
            return self::$INT_FREQUENCY_MONTH;
        }
        elseif($enumFrequency->equals(DatePeriodEnum::WEEK())) {
            return self::$INT_FREQUENCY_WEEK;
        }
        elseif($enumFrequency->equals(DatePeriodEnum::DAY())) {
            return self::$INT_FREQUENCY_DAY;
        }

        return null;
    }

}