<?php

namespace Derakht\Jalali\Traits;

trait Converter
{
    public function toJalaliDateString(): string
    {
        $this->updateJalali();

        return $this->formatJalali($this->jYear, $this->jMonth, $this->jDay);
    }

    public function toJalaliDateTimeString(): string
    {
        $this->updateJalali();

        return $this->formatJalali($this->jYear, $this->jMonth, $this->jDay).' '.$this->toTimeString();
    }

    private function formatJalali(int $year, int $month, int $day): string
    {
        $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        return implode('/', [$year, $month, $day]);
    }
}
