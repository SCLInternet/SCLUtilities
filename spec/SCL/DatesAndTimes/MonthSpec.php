<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\OutOfRangeException;
use SCL\DatesAndTimes\Month;
use PhpSpec\ObjectBehavior;

class MonthSpec extends ObjectBehavior
{
    public function it_throws_if_month_number_is_below_1()
    {
        $this->shouldThrow(new OutOfRangeException('Month must be between 1 and 12; got "0"'))
            ->during('__construct', [0]);
    }

    public function it_throws_if_month_number_is_above_12()
    {
        $this->shouldThrow(new OutOfRangeException('Month must be between 1 and 12; got "13"'))
            ->during('__construct', [13]);
    }

    public function it_stores_january()
    {
        $this->beConstructedWith(Month::JANUARY);

        $this->getValue()->shouldReturn(1);
    }

    public function it_stores_february()
    {
        $this->beConstructedWith(Month::FEBRUARY);

        $this->getValue()->shouldReturn(2);
    }

    public function it_stores_march()
    {
        $this->beConstructedWith(Month::MARCH);

        $this->getValue()->shouldReturn(3);
    }

    public function it_stores_april()
    {
        $this->beConstructedWith(Month::APRIL);

        $this->getValue()->shouldReturn(4);
    }

    public function it_stores_may()
    {
        $this->beConstructedWith(Month::MAY);

        $this->getValue()->shouldReturn(5);
    }

    public function it_stores_june()
    {
        $this->beConstructedWith(Month::JUNE);

        $this->getValue()->shouldReturn(6);
    }

    public function it_stores_july()
    {
        $this->beConstructedWith(Month::JULY);

        $this->getValue()->shouldReturn(7);
    }

    public function it_stores_august()
    {
        $this->beConstructedWith(Month::AUGUST);

        $this->getValue()->shouldReturn(8);
    }

    public function it_stores_september()
    {
        $this->beConstructedWith(Month::SEPTEMBER);

        $this->getValue()->shouldReturn(9);
    }

    public function it_stores_october()
    {
        $this->beConstructedWith(Month::OCTOBER);

        $this->getValue()->shouldReturn(10);
    }

    public function it_stores_november()
    {
        $this->beConstructedWith(Month::NOVEMBER);

        $this->getValue()->shouldReturn(11);
    }

    public function it_stores_december()
    {
        $this->beConstructedWith(Month::DECEMBER);

        $this->getValue()->shouldReturn(12);
    }

    public function it_returns_integer()
    {
        $this->beConstructedWith('4');

        $this->getValue()->shouldReturn(4);
    }
}
