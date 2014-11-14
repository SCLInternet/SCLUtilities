<?php
namespace SCL\DatesAndTimes\Exception;

use SCL\Exception\ExceptionFactory;

class InvalidMinuteException extends \LogicException
{
    use ExceptionFactory;

    /** @throws self */
    public static function minuteOutOfRange($minute)
    {
        return self::create("Minute '%d' is out of range", [$minute]);
    }
}
