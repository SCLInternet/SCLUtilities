<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidStringFormatException;
use SCL\DatesAndTimes\Hour;
use SCL\DatesAndTimes\Minute;
use SCL\DatesAndTimes\Second;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SCL\DatesAndTimes\Time;

class TimeSpec extends ObjectBehavior
{
    public function it_returns_parts()
    {
        $this->itConstructsWith(11, 30, 40);

        $this->getHour()->shouldReturn(11);
        $this->getMinute()->shouldReturn(30);
        $this->getSecond()->shouldReturn(40);
    }

    public function it_returns_seconds_as_zero()
    {
        $this->itConstructsWith(15, 36, 10);

        $this->getSecond()->shouldReturn(10);
    }

    public function it_constructs_from_string()
    {
        $this->beConstructedThrough('fromString', ['11:30:40']);

        $this->getHour()->shouldReturn(11);
        $this->getMinute()->shouldReturn(30);
        $this->getSecond()->shouldReturn(40);
    }

    public function it_returns_as_string()
    {
        $this->itConstructsWith(15, 36, 10);

        $this->__toString()->shouldReturn('15:36:10');
    }

    public function it_throws_for_incorrect_string_format()
    {
        $this->beConstructedThrough('fromString', ['11:30:40']);

        $this->shouldThrow(
            new InvalidStringFormatException(
                'Time string format must be "HH:MM:SS"; got "123abc"'
            )
        )->duringFromString('123abc');
    }

    public function it_formats_the_time()
    {
        $this->itConstructsWith(15, 36, 00);
        $this->formatted('g:i a')->shouldReturn('3:36 pm');
    }

    public function it_compares_equal_time_correctly()
    {
        $same = $this->createTime(15, 36, 12);
        $this->itConstructsWith(15, 36, 12);

        $this->isEqualTo($same)->shouldReturn(true);
        $this->isEqualTo($this)->shouldReturn(true);
    }

    public function it_compares_later_times_correctly()
    {
        $this->itConstructsWith(15, 36, 12);
        $later1 = $this->createTime(15, 36, 11);
        $later2 = $this->createTime(15, 35, 12);
        $later3 = $this->createTime(14, 36, 12);

        $this->isLaterThan($this)->shouldReturn(false);

        $this->isLaterThan($later1)->shouldReturn(true);
        $this->isLaterThan($later2)->shouldReturn(true);
        $this->isLaterThan($later3)->shouldReturn(true);
    }

    function it_compares_earlier_times_correctly()
    {
        $this->itConstructsWith(15, 36, 12);
        $earlier1 = $this->createTime(16, 36, 12);
        $earlier2 = $this->createTime(15, 37, 12);
        $earlier3 = $this->createTime(15, 36, 13);

        $this->isEarlierThan($this)->shouldReturn(false);

        $this->isEarlierThan($earlier1)->shouldReturn(true);
        $this->isEarlierThan($earlier2)->shouldReturn(true);
        $this->isEarlierThan($earlier3)->shouldReturn(true);
    }

    private function createTime($h, $m, $s)
    {
        return new Time(new Hour($h), new Minute($m), new Second($s));
    }

    private function itConstructsWith($hour, $minute, $second)
    {
        $this->beConstructedWith(new Hour($hour), new Minute($minute), new Second($second));
    }
}
