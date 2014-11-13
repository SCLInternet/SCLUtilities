<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidHourException;

class Hour
{
    /** @var int */
    private $hour;

    public function __construct($hour)
    {
        if ($hour > 23) {
            throw InvalidHourException::hourOutOfRange($hour);
        }
        $this->hour = (int) $hour;
    }

    public function getValue()
    {
        return $this->hour;
    }
}
