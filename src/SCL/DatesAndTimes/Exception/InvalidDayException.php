<?php
namespace SCL\DatesAndTimes\Exception;

use SCL\Exception\ExceptionFactory;

class InvalidDayException extends \LogicException
{
    use ExceptionFactory;

    /** @throws self */
    public static function dayOutOfRange($day)
    {
        return self::create("Day '%d' is not a valid day of the week", [$day]);
    }
}
