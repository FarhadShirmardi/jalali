<?php


namespace Derakht\Jalali\Tests;

use Derakht\Jalali\Jalali;
use PHPUnit\Framework\TestCase;

final class JalaliTest extends TestCase
{
    public function test_parse_jalali()
    {
        $jDate = Jalali::parseJalali('1400/07/06');
        $this->assertEquals('2021-09-28', $jDate->toDateString());

        $jDate = Jalali::parseJalali('1400/07/06 18', 'Y/m/d H');
        $this->assertEquals('2021-09-28', $jDate->toDateString());
        $this->assertEquals('2021-09-28 18:00:00', $jDate->toDateTimeString());

        $jDate = Jalali::parseJalali('1400/07/06 18:34', 'Y/m/d H:i');
        $this->assertEquals('2021-09-28', $jDate->toDateString());
        $this->assertEquals('2021-09-28 18:34:00', $jDate->toDateTimeString());

        $jDate = Jalali::parseJalali('1400/07/06 18:34:58', 'Y/m/d H:i:s');
        $this->assertEquals('2021-09-28', $jDate->toDateString());
        $this->assertEquals('2021-09-28 18:34:58', $jDate->toDateTimeString());
    }

    public function test_to_jalali_date_string()
    {
        $jDate = Jalali::parse('2021-09-28');
        $this->assertEquals('1400/07/06', $jDate->toJalaliDateString());

        $jDate = Jalali::parse('2021-09-28 18:34');
        $this->assertEquals('1400/07/06', $jDate->toJalaliDateString());

        $jDate = Jalali::parse('2021-09-28 18:34:58');
        $this->assertEquals('1400/07/06', $jDate->toJalaliDateString());
    }

    public function test_to_jalali_date_time_string()
    {
        $jDate = Jalali::parse('2021-09-28 18:34');
        $this->assertEquals('1400/07/06 18:34:00', $jDate->toJalaliDateTimeString());

        $jDate = Jalali::parse('2021-09-28 18:34:58');
        $this->assertEquals('1400/07/06 18:34:58', $jDate->toJalaliDateTimeString());
    }

    public function test_jalali_leap_year()
    {
        $jDate = Jalali::parse('2020-09-28 18:34');
        $this->assertTrue($jDate->isJalaliLeapYear());

        $jDate = Jalali::parse('2021-09-28 18:34');
        $this->assertFalse($jDate->isJalaliLeapYear());
    }

    public function test_month_name()
    {
        $jDate = Jalali::parse('2021-09-28 18:34');
        $this->assertEquals('مهر', $jDate->getMonthName());

        $jDate = Jalali::parseJalali('1400/09/06');
        $this->assertEquals('آذر', $jDate->getMonthName());
    }

    public function test_add_day()
    {
        $jDate = Jalali::parseJalali('1400/12/29');
        self::assertEquals('1401/01/01', $jDate->addJalaliDay()->toJalaliDateString());

        $jDate = Jalali::parseJalali('1400/12/29');
        self::assertEquals('1401/01/10', $jDate->addJalaliDays(10)->toJalaliDateString());
    }

    public function test_sub_day()
    {
        $jDate = Jalali::parseJalali('1400/01/01');
        self::assertEquals('1399/12/30', $jDate->subJalaliDay()->toJalaliDateString());

        $jDate = Jalali::parseJalali('1400/12/29');
        self::assertEquals('1400/12/19', $jDate->subJalaliDays(10)->toJalaliDateString());
    }

    public function test_add_month()
    {
        $jDate = Jalali::parseJalali('1400/06/31');
        self::assertEquals('1400/07/30', $jDate->addJalaliMonth()->toJalaliDateString());

        $jDate = Jalali::parseJalali('1399/10/15');
        self::assertEquals('1400/03/15', $jDate->addJalaliMonths(5)->toJalaliDateString());

        $jDate = Jalali::parseJalali('1399/10/15');
        self::assertEquals('1401/01/15', $jDate->addJalaliMonths(15)->toJalaliDateString());
    }

    public function test_sub_month()
    {
        $jDate = Jalali::parseJalali('1400/06/31');
        self::assertEquals('1400/05/31', $jDate->subJalaliMonth()->toJalaliDateString());

        $jDate = Jalali::parseJalali('1399/03/31');
        self::assertEquals('1398/10/30', $jDate->subJalaliMonths(5)->toJalaliDateString());
    }

    public function test_add_year()
    {
        $jDate = Jalali::parseJalali('1400/06/31');
        self::assertEquals('1401/06/31', $jDate->addJalaliYear()->toJalaliDateString());

        $jDate = Jalali::parseJalali('1399/12/30');
        self::assertEquals('1401/12/29', $jDate->addJalaliYears(2)->toJalaliDateString());
    }

    public function test_sub_year()
    {
        $jDate = Jalali::parseJalali('1400/06/31');
        self::assertEquals('1399/06/31', $jDate->subJalaliYears()->toJalaliDateString());

        $jDate = Jalali::parseJalali('1399/12/30');
        self::assertEquals('1396/12/29', $jDate->subJalaliYears(3)->toJalaliDateString());
    }
}