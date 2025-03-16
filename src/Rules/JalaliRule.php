<?php

namespace Derakht\Jalali\Rules;

use Derakht\Jalali\Jalali;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class JalaliRule implements Rule
{
    private string $format;

    public function __construct(string $format = 'Y/m/d')
    {
        $this->format = $format;
    }

    public function passes($attribute, $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        try {
            Jalali::parseFromFormat($value, $this->format);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return trans('validation.jalali_date_not_valid');
    }
}
