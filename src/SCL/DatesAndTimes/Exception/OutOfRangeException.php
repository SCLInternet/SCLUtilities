<?php

namespace SCL\DatesAndTimes\Exception;

class OutOfRangeException extends \OutOfRangeException
{
    use ExceptionFactory;

    /**
     * @param string $name
     * @param number $lower
     * @param number $upper
     * @param number $actual
     *
     * @return OutOfRangeException
     */
    public static function outOfRange($name, $lower, $upper, $actual)
    {
        return self::create(
            '%s must be between %s and %s; got "%s"',
            [$name, $lower, $upper, $actual]
        );
    }
}
