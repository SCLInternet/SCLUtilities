<?php
namespace SCL\DatesAndTimes\Exception;

class InvalidSecondException extends \LogicException
{
    use ExceptionFactory;

    /** @throws InvalidSecondException */
    public static function secondOutOfRange($second)
    {
        return self::create("Second '%d' is out of range", [$second]);
    }
}
