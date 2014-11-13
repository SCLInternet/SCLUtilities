<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidSecondException;

class Second
{
    /** @var int */
    private $second;

    public function __construct($second)
    {
        if ($second > 59) {
            throw InvalidSecondException::secondOutOfRange($second);
        }
        $this->second = 0;
    }

    public function getValue()
    {
        return $this->second;
    }
}
