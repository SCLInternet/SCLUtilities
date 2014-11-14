<?php

namespace SCL\DatesAndTimes\Exception;

use SCL\Exception\ExceptionFactory;

class PeriodTypeMismatchException extends \LogicException
{
    use ExceptionFactory;

    /**
     * @param string $actual
     * @param string $expected
     * @return self
     */
    public static function gotTypeButExpectedType($actual, $expected)
    {
        return self::create(
            "Got period of type %s, but expected %s",
            [$actual, $expected]
        );
    }
}
