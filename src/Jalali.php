<?php

namespace Derakht\Jalali;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Derakht\Jalali\Traits\Boundaries;
use Derakht\Jalali\Traits\Converter;
use Derakht\Jalali\Traits\Modifiers;

class Jalali extends Carbon
{
    use Boundaries;
    use Converter;
    use Modifiers;

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
            $skipCurrent = $lastChar == '\\';
            if (! $skipCurrent && isset($keys[$char])) {
                $regex .= '(?P<'.$keys[$char][0].'>'.$keys[$char][1].')';
            } else {
                if ($char == '\\') {
                    $regex .= $char;
                } else {
                    $regex .= preg_quote($char);
                }
            }
        }

        $dt = [];
        $dt['error_count'] = 0;
        // now try to match it
        if ($matched = preg_match('#^'.$regex.'$#', $date, $dt)) {
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
            $now = self::now();
            $now->updateJalali();
            $year = (int) substr((string) $now->jYear, 0, 2);
            $dt['year'] = ($dt['year'] > 50 ? $year - 1 : $year).$dt['year'];
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
        [$gYear, $gMonth, $gDay] = CalendarUtils::jalaliToGregorian($this->jYear, $this->jMonth, $this->jDay);
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
            )->map(fn ($item) => trim(implode('', $item))));

        $timeCollection = collect([
            '',
            'H',
            'H:i',
            'H:i:s',
        ]);

        $formats = $dateCollection
            ->crossJoin([' '], $timeCollection)
            ->map(fn ($item) => trim(implode('', $item)))
            ->filter(fn ($item) => $item !== '')
            ->sortByDesc(fn ($item) => strlen($item));
        foreach ($formats as $format) {
            try {
                return self::parseFromFormat($date, $format);
            } catch (InvalidFormatException) {
                continue;
            }
        }

        throw new InvalidFormatException;
    }

    public function updateJalali(): void
    {
        [$jYear, $jMonth, $jDay] = CalendarUtils::gregorianToJalali($this->year, $this->month, $this->day);
        $this->setJalaliDate($jYear, $jMonth, $jDay);
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
}
