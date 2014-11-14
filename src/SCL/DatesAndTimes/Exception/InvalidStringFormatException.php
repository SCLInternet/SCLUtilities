<?php

namespace SCL\DatesAndTimes\Exception;

use SCL\Exception\ExceptionFactory;

class InvalidStringFormatException extends \LogicException
{
    use ExceptionFactory;

    /**
     * @param string $name
     * @param string $expectedFormat
     * @param string $actual
     *
     * @return InvalidStringFormatException
     */
    public static function invalidFormat($name, $expectedFormat, $actual)
    {
        return self::create(
            '%s string format must be "%s"; got "%s"',
            [$name, $expectedFormat, $actual]
        );
    }
}
