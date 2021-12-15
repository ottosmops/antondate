<?php

namespace Ottosmops\Antondate\Facades;

use Illuminate\Support\Facades\Facade;

class Antondate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'antondate';
    }
}
