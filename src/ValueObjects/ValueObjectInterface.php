<?php

namespace Ottosmops\Antondate\ValueObjects;

interface ValueObjectInterface
{
    /**
     * Create a object from the PHP native value.
     *
     * @return obj
     */
    public static function createFromString(string $value);

    /**
     * Validates the string representation of the value.
     * @param  mixed  $value
     * @return boolean
     */
    public static function isValidString(string $value);

    /**
     * Returns the value of the object
     *
     * @return mixed
     */
    public function toString();

    /**
     * Serializes the object to a string
     * @return string
     */
    public function __toString();
}
