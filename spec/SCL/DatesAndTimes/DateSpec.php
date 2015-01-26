<?php

namespace spec\SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidDateException;
use SCL\DatesAndTimes\Exception\InvalidStringFormatException;
use SCL\DatesAndTimes\Date;
use SCL\DatesAndTimes\Month;
use SCL\DatesAndTimes\Year;
use PhpSpec\ObjectBehavior;
use SCL\PHPSpec\Matchers;

class DateSpec extends ObjectBehavior
{
    public function it_returns_parts()
    {
        $year  = new Year(2010);
        $month = new Month(Month::FEBRUARY);

        $this->beConstructedWith($year, $month, 20);

        $this->getDay()->shouldReturn(20);
        $this->getMonth()->shouldReturn($month);
        $this->getYear()->shouldReturn($year);
    }

    public function it_returns_day_as_integer()
    {
        $this->beSetupWithDate(2010, Month::FEBRUARY, '20');

        $this->getDay()->shouldReturn(20);
    }

    public function it_converts_to_string()
    {
        $this->beSetupWithDate(2010, Month::FEBRUARY, 20);

        $this->__toString()->shouldReturn('2010-02-20');
    }

    public function it_constructs_from_string()
    {
        $this->keepPhpSpecHappyForStaticCalls();

        $date = $this::fromString('2013-03-11');

        $date->__toString()->shouldReturn('2013-03-11');
    }

    public function it_constructs_another_date_from_string()
    {
        $this->keepPhpSpecHappyForStaticCalls();

        $date = $this::fromString('2014-04-19');

        $date->__toString()->shouldReturn('2014-04-19');
    }

    public function it_throws_for_incorrect_string_format()
    {
        $this->keepPhpSpecHappyForStaticCalls();

        $this->shouldThrow(
            new InvalidStringFormatException(
                'Date string format must be "YYYY-MM-DD"; got "123abc"'
            )
        )->duringFromString('123abc');
    }

    public function it_tests_if_this_year_is_before_another_year()
    {
        $this->beSetupWithDate(2014, 05, 15);

        $this->shouldBeBefore(Date::fromString('2014-05-16'));
        $this->shouldNotBeBefore(Date::fromString('2014-05-15'));
        $this->shouldNotBeBefore(Date::fromString('2014-05-14'));
    }

    public function it_tests_if_this_year_is_before_or_the_same_as_another()
    {
        $this->beSetupWithDate(2014, 05, 15);

        $this->shouldBeBeforeOrSameAs(Date::fromString('2014-05-16'));
        $this->shouldBeBeforeOrSameAs(Date::fromString('2014-05-15'));
        $this->shouldNotBeBeforeOrSameAs(Date::fromString('2014-05-14'));
    }

    public function it_tests_if_this_year_is_after_another_year()
    {
        $this->beSetupWithDate(2014, 05, 15);

        $this->shouldNotBeAfter(Date::fromString('2014-05-16'));
        $this->shouldNotBeAfter(Date::fromString('2014-05-15'));
        $this->shouldBeAfter(Date::fromString('2014-05-14'));
    }

    public function it_tests_if_this_year_is_after_or_the_sames_as_another_year()
    {
        $this->beSetupWithDate(2014, 05, 15);

        $this->shouldNotBeAfterOrSameAs(Date::fromString('2014-05-16'));
        $this->shouldBeAfterOrSameAs(Date::fromString('2014-05-15'));
        $this->shouldBeAfterOrSameAs(Date::fromString('2014-05-14'));
    }

    public function it_throws_for_invalid_date()
    {
        $this->shouldThrow(new InvalidDateException('2014-02-29 is not a valid date'))
            ->during('__construct', [new Year('2014'), new Month(Month::FEBRUARY), 29]);
    }

    public function it_calculates_yesterday()
    {
        $this->beSetupWithDate(2014, 01, 01);
        $this->yesterday()->__toString()->shouldReturn('2013-12-31');
    }

    public function it_calculates_tomorrow()
    {
        $this->beSetupWithDate(2013, 12, 31);
        $this->tomorrow()->__toString()->shouldReturn('2014-01-01');
    }

    public function it_is_the_same_as_itself()
    {
        $this->beSetupWithDate(2014, 01, 01);
        $this->shouldBeSameAs($this);
    }

    public function it_returns_a_subclass_instance_from_modify()
    {
        $this->beAnInstanceOf('spec\SCL\DatesAndTimes\DateSubclass');
        $this->beSetupWithDate(2014, 01, 01);

        $this->modify('today')->shouldReturnAnInstanceOf('spec\SCL\DatesAndTimes\DateSubclass');
    }

    private function keepPhpSpecHappyForStaticCalls()
    {
        $this->beSetupWithDate(2000, 01, 01);
    }

    /**
     * @param int $year
     * @param int $month
     * @param mixed $day
     */
    public function beSetupWithDate($year, $month, $day)
    {
        $this->beConstructedWith(new Year($year), new Month($month), $day);
    }

    public function it_formats_a_date()
    {
        $this->beSetupWithDate(2014, 10, 31);
        $this->formatted('Y-n-j')->shouldReturn('2014-10-31');
        $this->formatted('l, jS F Y')->shouldReturn('Friday, 31st October 2014');
    }

    public function it_counts_days_until_date()
    {
        $this->beConstructedThrough('fromString', ['2015-01-01']);
        $this->daysUntil(Date::fromString('2015-01-01'))->shouldReturn(0);
        $this->daysUntil(Date::fromString('2015-01-02'))->shouldReturn(1);
        $this->daysUntil(Date::fromString('2016-02-02'))->shouldReturn(397);
        $this->daysUntil(Date::fromString('2014-12-31'))->shouldReturn(-1);
    }

    public function it_counts_weeks_until_date()
    {
        $this->beConstructedThrough('fromString', ['2015-01-01']);
        $this->weeksUntil(Date::fromString('2015-01-01'))->shouldReturnArray([0, 0]);
        $this->weeksUntil(Date::fromString('2015-01-08'))->shouldReturnArray([1, 0]);
        $this->weeksUntil(Date::fromString('2015-01-09'))->shouldReturnArray([1, 1]);
        $this->weeksUntil(Date::fromString('2014-12-25'))->shouldReturnArray([-1, 0]);
        $this->weeksUntil(Date::fromString('2014-12-26'))->shouldReturnArray([0, -6]);
        $this->weeksUntil(Date::fromString('2014-12-24'))->shouldReturnArray([-1, -1]);
    }

    public function getMatchers()
    {
        return ['returnArray' => Matchers::returnArray()];
    }
}

class DateSubclass extends Date
{
}
