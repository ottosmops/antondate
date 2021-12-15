<?php

namespace Ottosmops\Antondate\Casts;

use InvalidArgumentException;
use Ottosmops\Antondate\ValueObjects\AntonDate;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AntonDateStartCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return AntonDate;
     */
    public function get($model, $key, $value, $attributes)
    {
        return AntonDate::createFromString(
            $attributes['date_start'] ?? '0000',
            $attributes['date_start_ca'] ?? 0,
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  AntonDate $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        /*
         * We'll need this to handle nullable columns
         */
        if (is_null($value)) {
            return;
        }

        if (!$value instanceof AntonDate) {
            throw new InvalidArgumentException(
                'The given value is not a AntonDate instance.'
            );
        }

        return [
            'date_start' => $value->toMysqlDate(),
            'date_start_ca' => $value->getCa(),
        ];
    }
}
