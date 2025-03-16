<?php

namespace Derakht\Jalali;

class CalendarUtils
{
    public const DaysOfMonthG = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    public const DaysOfMonthJ = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

    /**
     * Converts gregorian year, month, day to jalali ones.
     */
    public static function gregorianToJalali(int $year, int $month, int $day): array
    {
        $gYear = $year - 1600;
        $gMonth = $month - 1;
        $gDay = $day - 1;

        $gDayNo =
            365 * $gYear + self::div($gYear + 3, 4) - self::div($gYear + 99, 100) + self::div($gYear + 399, 400);

        for ($i = 0; $i < $gMonth; $i++) {
            $gDayNo += self::DaysOfMonthG[$i];
        }
        if ($gMonth > 1 && (($gYear % 4 == 0 && $gYear % 100 != 0) || ($gYear % 400 == 0))) {
            /* leap and after Feb */
            $gDayNo++;
        }
        $gDayNo += $gDay;

        $jDayNo = $gDayNo - 79;

        $jNp = self::div($jDayNo, 12053); /* 12053 = 365*33 + 32/4 */
        $jDayNo %= 12053;

        $jy = 979 + 33 * $jNp + 4 * self::div($jDayNo, 1461); /* 1461 = 365*4 + 4/4 */

        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jy += self::div($jDayNo - 1, 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        for ($i = 0; $i < 11 && $jDayNo >= self::DaysOfMonthJ[$i]; $i++) {
            $jDayNo -= self::DaysOfMonthJ[$i];
        }
        $jm = $i + 1;
        $jd = $jDayNo + 1;

        return [$jy, $jm, $jd];
    }

    public static function div(int $a, int $b): int
    {
        return (int) ($a / $b);
    }

    public static function jalaliToGregorian(int|string $year, int|string $month, int|string $day): array
    {
        $jYear = (int) ($year) - 979;
        $jMonth = (int) ($month) - 1;
        $jDay = (int) ($day) - 1;

        $jDayNo = 365 * $jYear + self::div($jYear, 33) * 8 + self::div($jYear % 33 + 3, 4);

        for ($i = 0; $i < $jMonth; $i++) {
            $jDayNo += self::DaysOfMonthJ[$i];
        }

        $jDayNo += $jDay;

        $gDayNo = $jDayNo + 79;

        $gy = 1600 + 400 * self::div($gDayNo, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
        $gDayNo %= 146097;

        $leap = true;
        if ($gDayNo >= 36525) {
            /* 36525 = 365*100 + 100/4 */
            $gDayNo--;
            $gy += 100 * self::div($gDayNo, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
            $gDayNo %= 36524;

            if ($gDayNo >= 365) {
                $gDayNo++;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * self::div($gDayNo, 1461); /* 1461 = 365*4 + 4/4 */
        $gDayNo %= 1461;

        if ($gDayNo >= 366) {
            $leap = false;

            $gDayNo--;
            $gy += self::div($gDayNo, 365);
            $gDayNo %= 365;
        }

        for ($i = 0; $gDayNo >= self::DaysOfMonthG[$i] + ($i == 1 && $leap); $i++) {
            $gDayNo -= self::DaysOfMonthG[$i] + ($i == 1 && $leap);
        }
        $gm = $i + 1;
        $gd = $gDayNo + 1;

        return [$gy, $gm, $gd];
    }

    public static function getDayCount(int $year, int $month): int
    {
        return $month <= 6 ? 31 : ($month < 12 ? 30 : (self::isLeapYear($year) ? 30 : 29));
    }

    public static function isLeapYear(int $year): bool
    {
        return in_array(($year % 33), [1, 5, 9, 13, 17, 22, 26, 30]);
    }

    public static function getMonthName(int|string $month): string
    {
        return match ((int) $month) {
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
            default => '',
        };
    }

    public static function checkDate(int $year, int $month, int $day): bool
    {
        return $year >= 1 and $month >= 1 and $month <= 12
            and $day >= 1 and $day <= self::getDayCount($year, $month);
    }
}
