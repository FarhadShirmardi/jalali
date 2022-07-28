<?php

namespace Derakht\Jalali;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class Jalali extends Carbon
{
    public int $jYear;
    public int $jMonth;
    public int $jDay;

    public static function parseJalali(string $datetime, ?string $format = null): Jalali
    {
        [$year, $month, $day, $time] = self::parseFromFormat($datetime, $format);

        if ($time) {
            $time = self::now()->setTimeFromTimeString($time);
        } else {
            $time = self::now();
        }

        return static::now()->setJalaliDate($year, $month, $day)->setTimeFrom($time);
    }

    public static function parseFromFormat(string $date, ?string $format = null): array
    {
        if (empty($format)) {
            return static::guessFormat($date);
        }
        $keys = [
            'Y' => ['year', '\d{4}'],
            'y' => ['year', '\d{2}'],
            'm' => ['month', '\d{2}'],
            'n' => ['month', '\d{1,2}'],
            'M' => ['month', '[A-Z][a-z]{3}'],
            'F' => ['month', '[A-Z][a-z]{2,8}'],
            'd' => ['day', '\d{2}'],
            'j' => ['day', '\d{1,2}'],
            'D' => ['day', '[A-Z][a-z]{2}'],
            'l' => ['day', '[A-Z][a-z]{6,9}'],
            'u' => ['hour', '\d{1,6}'],
            'h' => ['hour', '\d{2}'],
            'H' => ['hour', '\d{2}'],
            'g' => ['hour', '\d{1,2}'],
            'G' => ['hour', '\d{1,2}'],
            'i' => ['minute', '\d{2}'],
            's' => ['second', '\d{2}'],
        ];

        // convert format string to regex
        $regex = '';
        $chars = str_split($format);
        foreach ($chars as $n => $char) {
            $lastChar = $chars[$n - 1] ?? '';
            $skipCurrent = '\\' == $lastChar;
            if (! $skipCurrent && isset($keys[$char])) {
                $regex .= '(?P<' . $keys[$char][0] . '>' . $keys[$char][1] . ')';
            } else {
                if ('\\' == $char) {
                    $regex .= $char;
                } else {
                    $regex .= preg_quote($char);
                }
            }
        }

        $dt = [];
        $dt['error_count'] = 0;
        // now try to match it
        if ($matched = preg_match('#^' . $regex . '$#', $date, $dt)) {
            foreach ($dt as $k => $v) {
                if (is_int($k)) {
                    unset($dt[$k]);
                }
            }
        }

        if (! $matched) {
            throw new InvalidFormatException('Invalid date format!');
        }

        if ((isset($dt['year'], $dt['month'], $dt['day']) and ! CalendarUtils::checkDate($dt['year'], $dt['month'], $dt['day']))) {
            throw new InvalidFormatException('Invalid date format!');
        }

        if (isset($dt['year']) and strlen($dt['year']) == 2) {
            $dt['year'] = substr((string) self::now()->jYear, 0, 2) . $dt['year'];
        }

        $year = isset($dt['year']) ? (int) $dt['year'] : 0;
        $month = isset($dt['month']) ? (int) $dt['month'] : 0;
        $day = isset($dt['day']) ? (int) $dt['day'] : 0;
        $hour = isset($dt['hour']) ? (int) $dt['hour'] : 0;
        $minute = isset($dt['minute']) ? (int) $dt['minute'] : 0;
        $second = isset($dt['second']) ? (int) $dt['second'] : 0;


        return [$year, $month, $day, (new self)->setTime($hour, $minute, $second)->toTimeString()];
    }

    public function setJalaliDate(int|string $jYear, int|string $jMonth, int|string $jDay): Jalali
    {
        $this->jYear = (int) $jYear;
        $this->jMonth = (int) $jMonth;
        $this->jDay = (int) $jDay;

        $this->updateGregorian();

        return $this;
    }

    private function updateGregorian(): void
    {
        [$gYear, $gMonth, $gDay] = CalendarUtils::j2g($this->jYear, $this->jMonth, $this->jDay);
        $this->setDate($gYear, $gMonth, $gDay);
    }

    protected static function guessFormat(string $date): array
    {
        $separator = ['/', '-'];
        $dateCollection = collect([''])
            ->merge(collect(['y', 'Y'])->crossJoin(
                $separator,
                ['m', 'n'],
                $separator,
                ['d', 'j']
            )->map(fn($item) => trim(implode('', $item))));

        $timeCollection = collect([
            '',
            'H',
            'H:i',
            'H:i:s',
        ]);

        $formats = $dateCollection
            ->crossJoin([' '], $timeCollection)
            ->map(fn($item) => trim(implode('', $item)))
            ->filter(fn($item) => $item !== '')
            ->sortByDesc(fn($item) => strlen($item));
        foreach ($formats as $format) {
            try {
                return self::parseFromFormat($date, $format);
            } catch (InvalidFormatException) {
                continue;
            }
        }

        throw new InvalidFormatException();
    }

    public function toJalaliDateString(): string
    {
        $this->updateJalali();
        return $this->formatJalali($this->jYear, $this->jMonth, $this->jDay);
    }

    private function updateJalali(): void
    {
        [$jYear, $jMonth, $jDay] = CalendarUtils::g2j($this->year, $this->month, $this->day);
        $this->setJalaliDate($jYear, $jMonth, $jDay);
    }

    private function formatJalali(int $year, int $month, int $day): string
    {
        $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        return implode('/', [$year, $month, $day]);
    }

    public function toJalaliDateTimeString(): string
    {
        $this->updateJalali();
        return $this->formatJalali($this->jYear, $this->jMonth, $this->jDay) . ' ' . $this->toTimeString();
    }

    public function getMonthName(): string
    {
        $this->updateJalali();
        return CalendarUtils::getMonthName($this->jMonth);
    }

    public function isJalaliLeapYear(): bool
    {
        $this->updateJalali();
        return CalendarUtils::isLeapYear($this->jYear);
    }

    public function addJalaliDay(): Jalali
    {
        return $this->addJalaliDays();
    }

    public function addJalaliDays(int $days = 1): Jalali
    {
        parent::addDays($days);
        $this->updateJalali();
        return $this;
    }

    public function addJalaliMonth(): Jalali
    {
        return $this->addJalaliMonths();
    }

    public function addJalaliMonths(int $months = 1): Jalali
    {
        $years = (int) ($months / 12);
        $addMonths = $months % 12;
        if ($years > 0) {
            $this->addJalaliYears($years);
        }

        while ($addMonths > 0) {
            $nextMonth = ($this->jMonth + 1) % 12;
            $nextMonthDayCount = CalendarUtils::getDayCount($this->jYear, $nextMonth === 0 ? 12 : $nextMonth);
            $nextMonthDay = min($this->jDay, $nextMonthDayCount);

            $days = ($this->getMonthDays() - $this->jDay) + $nextMonthDay;

            $this->addJalaliDays($days);
            $addMonths--;
        }

        $this->updateGregorian();
        return $this;
    }

    public function addJalaliYears(int $years = 1): Jalali
    {
        $this->jYear += $years;
        if (CalendarUtils::isLeapYear($this->jYear) === false and
            $this->jMonth === 12 and
            $this->jDay > CalendarUtils::getDayCount($this->jYear, 12)
        ) {
            $this->jDay = 29;
        }

        $this->updateGregorian();
        return $this;
    }

    public function getMonthDays(): int
    {
        return CalendarUtils::getDayCount($this->jYear, $this->jMonth);
    }

    public function addJalaliYear(): Jalali
    {
        return $this->addJalaliYears();
    }

    public function subJalaliDay(): Jalali
    {
        return $this->subJalaliDays();
    }

    public function subJalaliDays(int $days = 1): Jalali
    {
        parent::subDays($days);
        $this->updateJalali();
        return $this;
    }

    public function subJalaliMonth(): Jalali
    {
        return $this->subJalaliMonths();
    }

    public function subJalaliMonths(int $months = 1): Jalali
    {
        $diff = ($this->jMonth - $months);

        if ($diff >= 1) {
            $dayCount = CalendarUtils::getDayCount($this->jYear, $diff);
            $targetDay = min($this->jDay, $dayCount);
            $this->setJalaliDate($this->jYear, $diff, $targetDay);
            return $this;
        }

        $years = (int) (abs($diff) / 12);
        if ($years > 0) {
            $this->subJalaliYears($years);
        }
        $diff = 12 - abs($diff % 12) - $this->jMonth;

        return $diff > 0 ? $this->subJalaliYear()->addJalaliMonths($diff) : $this->subJalaliYear();
    }

    public function subJalaliYears(int $years = 1): Jalali
    {
        $this->jYear -= $years;
        if (CalendarUtils::isLeapYear($this->jYear) === false and
            $this->jMonth === 12 and
            $this->jDay >= CalendarUtils::getDayCount($this->jYear, 12)
        ) {
            $this->jDay = 29;
        }

        $this->updateGregorian();
        return $this;
    }

    public function subJalaliYear(): Jalali
    {
        return $this->subJalaliYears();
    }
}