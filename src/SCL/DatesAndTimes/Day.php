<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidDayException;

class Day
{
    /** @var string[] */
    private static $validDays = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];

    /** @var string */
    private $day;

    /** @var int */
    private $dayNumber;

    /** string $day */
    public function __construct($day)
    {
        $this->validate($day);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->day;
    }

    public function isSameDay(Day $candidate)
    {
        return (strcmp($this->__toString(), $candidate->__toString())) == 0;
    }

    /** @param string $day */
    private function validate($day)
    {
        $position = 0;
        foreach (self::$validDays as $pos => $validDay) {
            if (strcmp($day, $validDay) == 0) {
                $this->day = $day;
                $this->dayNumber = $position;
                return;
            }
        }
        throw InvalidDayException::dayOutOfRange($day);
    }
}
