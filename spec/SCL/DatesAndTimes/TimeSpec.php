<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidStringFormatException;
use SCL\DatesAndTimes\Hour;
use SCL\DatesAndTimes\Minute;
use SCL\DatesAndTimes\Second;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TimeSpec extends ObjectBehavior
{
    public function it_returns_parts()
    {
        $this->itConstructsWith(11, 30, 40);

        $this->getHour()->shouldReturn(11);
        $this->getMinute()->shouldReturn(30);
        $this->getSecond()->shouldReturn(0);
    }

    public function it_returns_seconds_as_zero()
    {
        $this->itConstructsWith(15, 36, 10);

        $this->getSecond()->shouldReturn(0);
    }

    public function it_constructs_from_string()
    {
        $this->beConstructedThrough('fromString', ['11:30:40']);

        $this->getHour()->shouldReturn(11);
        $this->getMinute()->shouldReturn(30);
        $this->getSecond()->shouldReturn(0);
    }

    public function it_returns_as_string()
    {
        $this->itConstructsWith(15, 36, 10);

        $this->__toString()->shouldReturn('15:36:00');
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

    private function itConstructsWith($hour, $minute, $second)
    {
        $this->beConstructedWith(new Hour($hour), new Minute($minute), new Second($second));
    }
}
