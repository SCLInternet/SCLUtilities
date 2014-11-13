<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Day;
use SCL\DatesAndTimes\Exception\InvalidDayException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DaySpec extends ObjectBehavior
{
    const MONDAY = 'monday';
    const BADDAY = 'badday';

    function let()
    {
        $this->beConstructedWith(self::MONDAY);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('SCL\DatesAndTimes\Day');
    }

    function it_rejects_bad_day_name()
    {
        $this->shouldThrow(InvalidDayException::dayOutOfRange(self::BADDAY))->during('__construct', [self::BADDAY]);
    }

    function it_accepts_good_day_name()
    {
        $this->shouldNotThrow(InvalidDayException::dayOutOfRange(self::MONDAY))->during('__construct', [self::MONDAY]);
    }

    function it_recognises_same_days()
    {
        $this->isSameDay(new Day('monday'))->shouldReturn(true);
    }

    function it_recognises_different_days()
    {
        $this->isSameDay(new Day('tuesday'))->shouldReturn(false);
    }
}
