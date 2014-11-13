<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidMinuteException;

class Minute
{
    /** @var int */
    private $minute;

    public function __construct($minute)
    {
        if ($minute > 59) {
            throw InvalidMinuteException::minuteOutOfRange($minute);
        }
        $this->minute = (int) $minute;
    }

    public function getValue()
    {
        return $this->minute;
    }
}
