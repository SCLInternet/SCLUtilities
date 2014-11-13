<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\OutOfRangeException;

class Month
{
    const JANUARY   = 1;
    const FEBRUARY  = 2;
    const MARCH     = 3;
    const APRIL     = 4;
    const MAY       = 5;
    const JUNE      = 6;
    const JULY      = 7;
    const AUGUST    = 8;
    const SEPTEMBER = 9;
    const OCTOBER   = 10;
    const NOVEMBER  = 11;
    const DECEMBER  = 12;

    /** @var int */
    private $value;

    /**
     * @param int $value
     *
     * @throws OutOfRangeException
     */
    public function __construct($value)
    {
        $this->assertIsWithinRange($value);

        $this->value = (int)$value;
    }

    /** @return int */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @throws OutOfRangeException
     */
    private function assertIsWithinRange($value)
    {
        if ($value < 1 || $value > 12) {
            throw OutOfRangeException::outOfRange(
                'Month',
                1,
                12,
                $value
            );
        }
    }
}
