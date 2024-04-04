<?php

namespace Ottosmops\Antondate\Rules;

use DateTimeImmutable;
use Illuminate\Contracts\Validation\Rule;
use Ottosmops\Antondate\ValueObjects\AntonDate;

class AntonDateRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value instanceof AntonDate) {
            return true;
        }
        if ($value instanceOf DateTimeImmutable) {
            return true;
        }
        if (is_int($value)) {
            $value = (string) $value;
        }
        if (!is_string($value)) {
            return false;
        }
        try {
            AntonDate::createFromString($value);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute is not an AntonDate.';
    }

    public function __toString() {
        return "AntonDate";
    }
}
