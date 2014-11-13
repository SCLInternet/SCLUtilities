<?php
namespace SCL\DatesAndTimes\Exception;

class InvalidDayException extends \LogicException
{
    use ExceptionFactory;

    /** @throws self */
    public static function dayOutOfRange($day)
    {
        return self::create("Day '%d' is not a valid day of the week", [$day]);
    }
}
