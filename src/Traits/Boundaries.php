<?php

namespace Derakht\Jalali\Traits;

use Derakht\Jalali\CalendarUtils;

trait Boundaries
{
    public function startOfJalaliMonth(): static
    {
        return $this->setJalaliDate($this->jYear, $this->jMonth, 1)->startOfDay();
    }

    public function endOfJalaliMonth(): static
    {
        return $this->setJalaliDate(
            $this->jYear,
            $this->jMonth,
            CalendarUtils::getDayCount($this->jYear, $this->jMonth)
        )->endOfDay();
    }

    public function startOfJalaliYear(): static
    {
        return $this->setJalaliDate($this->jYear, 1, 1)->startOfDay();
    }

    public function endOfJalaliYear(): static
    {
        return $this->setJalaliDate(
            $this->jYear,
            12,
            CalendarUtils::getDayCount($this->jYear, 12)
        )->endOfDay();
    }
}
