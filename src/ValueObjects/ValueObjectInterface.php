<?php

namespace Ottosmops\Antondate\ValueObjects;

interface ValueObjectInterface
{
    /**
     * Create a object from the PHP native value.
     *
     * @return static
     */
    public static function createFromString(string $value);

    /**
     * Validates the string representation of the value.
     * @param  string $value
     * @return boolean
     */
    public static function isValidString(string $value);

    /**
     * Returns the value of the object
     *
     * @return string
     */
    public function toString();

    /**
     * Serializes the object to a string
     * @return string
     */
    public function __toString();
}
