<?php

namespace SCL\DatesAndTimes\Exception;

class InvalidDateException extends \LogicException
{
    use ExceptionFactory;

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return InvalidDateException
     */
    public static function invalidDate($year, $month, $day)
    {
        return self::create(
            '%04d-%02d-%02d is not a valid date',
            [$year, $month, $day]
        );
    }
}
