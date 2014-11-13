<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidMinuteException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MinuteSpec extends ObjectBehavior
{
    public function it_throws_if_out_of_range()
    {
        $this->shouldThrow(InvalidMinuteException::minuteOutOfRange(60))->during('__construct', [60]);
    }

    public function it_has_valid_value()
    {
        $this->beConstructedWith(30);
        $this->getValue()->shouldReturn(30);
    }
}
