<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\OutOfRangeException;

class Year
{
    /** these seem sensible for this application */
    const LOWER_LIMIT = 1900;
    const UPPER_LIMIT = 2100;

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
        if ($value < self::LOWER_LIMIT || $value > self::UPPER_LIMIT) {
            throw OutOfRangeException::outOfRange(
                'Year',
                self::LOWER_LIMIT,
                self::UPPER_LIMIT,
                $value
            );
        }
    }
}
