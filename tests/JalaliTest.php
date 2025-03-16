<?php

use Derakht\Jalali\Jalali;
use Derakht\Jalali\Rules\JalaliRule;

it('can parse jalali format')
    ->expect(Jalali::parseJalali('1400/07/06')->toDateString())
    ->toBe('2021-09-28')
    ->and(Jalali::parseJalali('1400/07/06 18')->toDateString())
    ->toBe('2021-09-28')
    ->and(Jalali::parseJalali('1400/07/06 18')->toDateTimeString())
    ->toBe('2021-09-28 18:00:00')
    ->and(Jalali::parseJalali('1400/07/06 18:34')->toDateString())
    ->toBe('2021-09-28')
    ->and(Jalali::parseJalali('1400/07/06 18:34')->toDateTimeString())
    ->toBe('2021-09-28 18:34:00')
    ->and(Jalali::parseJalali('1400/07/06 18:34:58')->toDateString())
    ->toBe('2021-09-28')
    ->and(Jalali::parseJalali('1400/07/06 18:34:58')->toDateTimeString())
    ->toBe('2021-09-28 18:34:58')
    ->and(Jalali::parseJalali('99/12/29')->toDateString())
    ->toBe('2021-03-19');

it('can convert to jalali date string')
    ->expect(Jalali::parse('2021-09-28')->toJalaliDateString())
    ->toBe('1400/07/06')
    ->and(Jalali::parse('2021-09-28 18:34')->toJalaliDateString())
    ->toBe('1400/07/06')
    ->and(Jalali::parse('2021-09-28 18:34:58')->toJalaliDateString())
    ->toBe('1400/07/06');

it('can convert to jalali date time string')
    ->expect(Jalali::parse('2021-09-28 18:34')->toJalaliDateTimeString())
    ->toBe('1400/07/06 18:34:00')
    ->and(Jalali::parse('2021-09-28 18:34:58')->toJalaliDateTimeString())
    ->toBe('1400/07/06 18:34:58');

it('can check leap year')
    ->expect(Jalali::parse('2020-09-28')->isJalaliLeapYear())
    ->toBeTrue()
    ->and(Jalali::parse('2021-09-28')->isJalaliLeapYear())
    ->toBeFalse();

it('can return month name')
    ->expect(Jalali::parse('2021-09-28 18:34')->getMonthName())
    ->toBe('مهر')
    ->and(Jalali::parseJalali('1400/09/06')->getMonthName())
    ->toBe('آذر');

it('can add jalali day')
    ->expect(Jalali::parseJalali('1400/12/29')->addJalaliDay()->toJalaliDateString())
    ->toBe('1401/01/01')
    ->and(Jalali::parseJalali('1400/12/29')->addJalaliDays(10)->toJalaliDateString())
    ->toBe('1401/01/10');

it('can sub jalali day')
    ->expect(Jalali::parseJalali('1400/01/01')->subJalaliDay()->toJalaliDateString())
    ->toBe('1399/12/30')
    ->and(Jalali::parseJalali('1400/12/29')->subJalaliDays(10)->toJalaliDateString())
    ->toBe('1400/12/19');

it('can add jalali month')
    ->expect(Jalali::parseJalali('1400/06/31')->addJalaliMonth()->toJalaliDateString())
    ->toBe('1400/07/30')
    ->and(Jalali::parseJalali('1399/10/15')->addJalaliMonths(5)->toJalaliDateString())
    ->toBe('1400/03/15')
    ->and(Jalali::parseJalali('1399/10/15')->addJalaliMonths(15)->toJalaliDateString())
    ->toBe('1401/01/15');

it('can sub jalali month')
    ->expect(Jalali::parseJalali('1400/06/31')->subJalaliMonth()->toJalaliDateString())
    ->toBe('1400/05/31')
    ->and(Jalali::parseJalali('1399/03/31')->subJalaliMonths(5)->toJalaliDateString())
    ->toBe('1398/10/30');

it('can add jalali year')
    ->expect(Jalali::parseJalali('1400/06/31')->addJalaliYear()->toJalaliDateString())
    ->toBe('1401/06/31')
    ->and(Jalali::parseJalali('1399/12/30')->addJalaliYears(2)->toJalaliDateString())
    ->toBe('1401/12/29');

it('can sub jalali year')
    ->expect(Jalali::parseJalali('1400/06/31')->subJalaliYears()->toJalaliDateString())
    ->toBe('1399/06/31')
    ->and(Jalali::parseJalali('1399/12/30')->subJalaliYears(3)->toJalaliDateString())
    ->toBe('1396/12/29');

it('can pass time format rule')
    ->expect((new JalaliRule('H:i'))->passes('', '13:10'))
    ->toBeTrue();

it('can go to start of month')
    ->expect(Jalali::parseJalali('1403/11/11')->startOfJalaliMonth()->toJalaliDateTimeString())
    ->toBe('1403/11/01 00:00:00')
    ->and(Jalali::parseJalali('1403/06/11')->startOfJalaliMonth()->toJalaliDateString())
    ->toBe('1403/06/01');

it('can go to end of month')
    ->expect(Jalali::parseJalali('1403/11/11')->endOfJalaliMonth()->toJalaliDateTimeString())
    ->toBe('1403/11/30 23:59:59')
    ->and(Jalali::parseJalali('1403/06/11')->endOfJalaliMonth()->toJalaliDateString())
    ->toBe('1403/06/31')
    ->and(Jalali::parseJalali('1403/12/11')->endOfJalaliMonth()->toJalaliDateString())
    ->toBe('1403/12/30')
    ->and(Jalali::parseJalali('1404/12/11')->endOfJalaliMonth()->toJalaliDateString())
    ->toBe('1404/12/29');


it('can go to start of year')
    ->expect(Jalali::parseJalali('1403/11/11')->startOfJalaliYear()->toJalaliDateTimeString())
    ->toBe('1403/01/01 00:00:00')
    ->and(Jalali::parseJalali('1403/06/11')->startOfJalaliYear()->toJalaliDateString())
    ->toBe('1403/01/01');

it('can go to end of year')
    ->expect(Jalali::parseJalali('1403/11/11')->endOfJalaliYear()->toJalaliDateTimeString())
    ->toBe('1403/12/30 23:59:59')
    ->and(Jalali::parseJalali('1403/06/11')->endOfJalaliYear()->toJalaliDateString())
    ->toBe('1403/12/30')
    ->and(Jalali::parseJalali('1404/12/11')->endOfJalaliYear()->toJalaliDateString())
    ->toBe('1404/12/29');
