<?php
namespace SCL\DatesAndTimes\Exception;

class InvalidHourException extends \LogicException
{
    use ExceptionFactory;

    /** @throws self */
    public static function hourOutOfRange($hour)
    {
        return self::create("Hour '%d' is out of range", [$hour]);
    }
}
