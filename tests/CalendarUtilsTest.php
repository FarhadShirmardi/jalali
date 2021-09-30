<?php


namespace Derakht\Jalali\Tests;

use Derakht\Jalali\CalendarUtils;
use PHPUnit\Framework\TestCase;

final class CalendarUtilsTest extends TestCase
{
    public function testG2J()
    {
        $this->assertSame(CalendarUtils::g2j(2021, 9, 28), [1400, 7, 6]);
    }

    public function testJ2G()
    {
        $this->assertSame(CalendarUtils::j2g(1400, 7, 6), [2021, 9, 28]);
    }

    public function testIsLeapYear()
    {
        $this->assertTrue(CalendarUtils::isLeapYear(1399));
        $this->assertFalse(CalendarUtils::isLeapYear(1400));
    }

    public function testDayCount()
    {
        $this->assertEquals(31, CalendarUtils::getDayCount(1400, 2));
        $this->assertEquals(30, CalendarUtils::getDayCount(1400, 7));
        $this->assertEquals(29, CalendarUtils::getDayCount(1400, 12));
        $this->assertEquals(30, CalendarUtils::getDayCount(1399, 12));
    }

    public function testMonthName()
    {
        $this->assertEquals('فروردین', CalendarUtils::getMonthName(1));
        $this->assertEquals('اردیبهشت', CalendarUtils::getMonthName(2));
        $this->assertEquals('اسفند', CalendarUtils::getMonthName(12));
    }
}