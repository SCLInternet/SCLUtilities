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

    const SUNDAY = 'sunday';

    const SATURDAY = 'saturday';

    function it_is_initializable()
    {
        $this->beConstructedWith(self::MONDAY);
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
        $this->beConstructedWith(self::MONDAY);
        $this->isSameDay(new Day(self::MONDAY))->shouldReturn(true);
    }

    function it_recognises_different_days()
    {
        $this->beConstructedWith(self::MONDAY);
        $this->isSameDay(new Day('tuesday'))->shouldReturn(false);
    }

    function it_knows_mondays_number()
    {
        $this->beConstructedWith(self::MONDAY);
        $this->toNumber()->shouldReturn(1);
    }

    function it_knows_saturdays_number()
    {
        $this->beConstructedWith(self::SATURDAY);
        $this->toNumber()->shouldReturn(6);
    }

    function it_knows_sundays_number()
    {
        $this->beConstructedWith(self::SUNDAY);
        $this->toNumber()->shouldReturn(0);
    }

}
