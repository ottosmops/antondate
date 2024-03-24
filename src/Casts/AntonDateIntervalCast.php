<?php

namespace Ottosmops\Antondate\Casts;

use InvalidArgumentException;
use Ottosmops\Antondate\ValueObjects\AntonDate;
use Ottosmops\Antondate\ValueObjects\AntonDateInterval;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AntonDateIntervalCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  mixed  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string|int|bool>  $attributes
     * @return AntonDateInterval;
     */
    public function get($model, $key, $value, $attributes) : AntonDateInterval
    {
        $anton_date_interval =  new AntonDateInterval(
            AntonDate::createFromString((string) $attributes['date_start'], (bool) $attributes['date_start_ca']),
            AntonDate::createFromString((string) $attributes['date_end'], (bool) $attributes['date_end_ca'])
        );

        return $anton_date_interval;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  mixed  $model
     * @param  string  $key
     * @param  AntonDateInterval $value
     * @param  array<string|int|bool>  $attributes
     * @return array<string|int|bool>
     */
    public function set($model, $key, $value, $attributes) : array
    {
        if (!$value instanceof AntonDateInterval) {
            throw new InvalidArgumentException(
                'The given value is not a AntonDateInterval instance.'
            );
        }

        return [
            'date_start' => $value->mysqlDateStart(),
            'date_start_ca' => $value->dateStartCa(),
            'date_end' => $value->mysqlDateEnd(),
            'date_end_ca' => $value->dateEndCa(),
        ];
    }
}
