<?php

namespace SCL\DatesAndTimes\Exception;

use SCL\Exception\ExceptionFactory;

class InvalidPeriodException extends \LogicException
{
    use ExceptionFactory;

    const END_BEFORE_START = 1;

    /** @return InvalidPeriodException */
    public static function endIsBeforeStart($start, $end)
    {
        return self::create(
            'The start date (%s) must be before the end date (%s)',
            [$start, $end],
            self::END_BEFORE_START
        );
    }
}
