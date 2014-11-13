<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidStringFormatException;
use SCL\DatesAndTimes\Date;
use SCL\DatesAndTimes\Time;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateTimeSpec extends ObjectBehavior
{
    public function let(Date $date, Time $time)
    {
        $this->beConstructedWith($date, $time);
    }

    public function it_returns_a_string(Date $date, Time $time)
    {
        $date->__toString()->willReturn('2014-02-14');
        $time->__toString()->willReturn('11:02:00');

        $this->__toString()->shouldReturn('2014-02-14 11:02:00');
    }

    public function it_returns_the_date(Date $date)
    {
        $this->getDate()->shouldReturn($date);
    }

    public function it_returns_the_time(Time $time)
    {
        $this->getTime()->shouldReturn($time);
    }

    public function it_constructs_through_a_string()
    {
        $this->beConstructedThrough('fromString', ['2014-02-14 11:30:40']);

        $this->__toString()->shouldReturn('2014-02-14 11:30:00');
    }

    public function it_throws_if_invalid_string()
    {
        $this->beConstructedThrough('fromString', ['2014-02-14 11:30:40']);

        $this->shouldThrow(
            InvalidStringFormatException::invalidFormat(
                'DateTime',
                'YYYY-MM-DD HH:MM:SS',
                '123 abc'
            )
        )->duringFromString('123 abc');
    }

    public function it_throws_if_valid_date_but_no_time()
    {
        $this->beConstructedThrough('fromString', ['2014-02-14 11:30:40']);

        $this->shouldThrow(
            InvalidStringFormatException::invalidFormat(
                'DateTime',
                'YYYY-MM-DD HH:MM:SS',
                '2014-05-02'
            )
        )->duringFromString('2014-05-02');
    }

    public function it_throws_if_valid_date_but_invalid_time()
    {
        $this->beConstructedThrough('fromString', ['2014-02-14 11:30:40']);

        $this->shouldThrow(
            InvalidStringFormatException::invalidFormat(
                'DateTime',
                'YYYY-MM-DD HH:MM:SS',
                '2014-05-02 asdasdasdasd'
            )
        )->duringFromString('2014-05-02 asdasdasdasd');
    }

    public function it_throws_if_invalid_date()
    {
        $this->beConstructedThrough('fromString', ['2014-02-14 11:30:40']);

        $this->shouldThrow(
            InvalidStringFormatException::invalidFormat(
                'DateTime',
                'YYYY-MM-DD HH:MM:SS',
                '2014-13-01 12:00:00'
            )
        )->duringFromString('2014-13-01 12:00:00');
    }
}
