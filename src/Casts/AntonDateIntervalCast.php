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
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return AntonDateInterval;
     */
    public function get($model, $key, $value, $attributes)
    {

        $anton_date_interval =  new AntonDateInterval(
            AntonDate::createFromString($attributes['date_start'], $attributes['date_start_ca']),
            AntonDate::createFromString($attributes['date_end'],$attributes['date_end_ca'])
        );
        if ($anton_date_interval == '0000') {
            return '';
        }
        return $anton_date_interval;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  Anton\AntonDateInterval $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
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
