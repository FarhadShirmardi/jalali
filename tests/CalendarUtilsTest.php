<?php

use Derakht\Jalali\CalendarUtils;

it('can convert gregorian to jalali')
    ->expect(CalendarUtils::g2j(2021, 9, 28))
    ->toBe([1400, 7, 6]);

it('can convert jalali to gregorian')
    ->expect(CalendarUtils::j2g(1400, 7, 6))
    ->toBe([2021, 9, 28]);

it('can check is leap year')
    ->expect(CalendarUtils::isLeapYear(1399))
    ->toBeTrue()
    ->and(CalendarUtils::isLeapYear(1400))
    ->toBeFalse();

it('can check day count')
    ->expect(CalendarUtils::getDayCount(1400, 2))
    ->toBe(31)
    ->and(CalendarUtils::getDayCount(1400, 7))
    ->toBe(30)
    ->and(CalendarUtils::getDayCount(1400, 12))
    ->toBe(29)
    ->and(CalendarUtils::getDayCount(1399, 12))
    ->toBe(30);

it('can check month name')
    ->expect(CalendarUtils::getMonthName(1))
    ->toBe('فروردین')
    ->and(CalendarUtils::getMonthName(2))
    ->toBe('اردیبهشت')
    ->and(CalendarUtils::getMonthName(3))
    ->toBe('خرداد')
    ->and(CalendarUtils::getMonthName(4))
    ->toBe('تیر')
    ->and(CalendarUtils::getMonthName(5))
    ->toBe('مرداد')
    ->and(CalendarUtils::getMonthName(6))
    ->toBe('شهریور')
    ->and(CalendarUtils::getMonthName(7))
    ->toBe('مهر')
    ->and(CalendarUtils::getMonthName(8))
    ->toBe('آبان')
    ->and(CalendarUtils::getMonthName(9))
    ->toBe('آذر')
    ->and(CalendarUtils::getMonthName(10))
    ->toBe('دی')
    ->and(CalendarUtils::getMonthName(11))
    ->toBe('بهمن')
    ->and(CalendarUtils::getMonthName(12))
    ->toBe('اسفند')
    ->and(CalendarUtils::getMonthName(13))
    ->toBe('')
    ->and(CalendarUtils::getMonthName(100))
    ->toBe('');