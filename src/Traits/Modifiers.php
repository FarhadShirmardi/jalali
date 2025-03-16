<?php

namespace Derakht\Jalali\Traits;

use Derakht\Jalali\CalendarUtils;
use Derakht\Jalali\Jalali;

trait Modifiers
{
    public function addJalaliDay(): static
    {
        return $this->addJalaliDays();
    }

    public function addJalaliDays(int $days = 1): static
    {
        parent::addDays($days);
        $this->updateJalali();

        return $this;
    }

    public function addJalaliMonth(): static
    {
        return $this->addJalaliMonths();
    }

    public function addJalaliMonths(int $months = 1): static
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

            $days = (CalendarUtils::getDayCount($this->jYear, $this->jMonth) - $this->jDay) + $nextMonthDay;

            $this->addJalaliDays($days);
            $addMonths--;
        }

        $this->updateGregorian();

        return $this;
    }

    public function addJalaliYears(int $years = 1): static
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

    public function addJalaliYear(): static
    {
        return $this->addJalaliYears();
    }

    public function subJalaliDay(): static
    {
        return $this->subJalaliDays();
    }

    public function subJalaliDays(int $days = 1): static
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
