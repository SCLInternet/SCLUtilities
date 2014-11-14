<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidSecondException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SecondSpec extends ObjectBehavior
{
    public function it_throws_if_out_of_range()
    {
        $this->shouldThrow(InvalidSecondException::secondOutOfRange(60))->during('__construct', [60]);
    }

    public function it_has_a_valid_value()
    {
        $this->beConstructedWith(30);
        $this->getValue()->shouldReturn(30);
    }
}
