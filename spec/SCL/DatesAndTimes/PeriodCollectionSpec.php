<?php

namespace spec\SCL\DatesAndTimes;

use SCL\PHPSpec\Matchers;
use SCL\DatesAndTimes\Exception\PeriodTypeMismatchException;
use SCL\DatesAndTimes\Period;
use SCL\DatesAndTimes\PeriodCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PeriodCollectionSpec extends ObjectBehavior
{
    public function it_has_a_zero_count_when_empty()
    {
        $this->count()->shouldReturn(0);
        $this->shouldBeEmpty();
    }

    public function it_counts_correctly()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-02-01', '2014-03-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->count()->shouldReturn(2);
        $this->shouldNotBeEmpty();
    }

    public function it_adds_a_period(Period $period)
    {
        $this->add($period->getWrappedObject());
        $this->count()->shouldReturn(1);
    }

    public function it_does_not_throw_if_adding_the_same_type_of_Period(
        Period $period
    ) {
        $this->add($period->getWrappedObject());
        $this->shouldNotThrow(new \LogicException())
            ->during('add', [$period->getWrappedObject()]);
    }

    public function it_returns_an_array()
    {
        list($period1, $period2) = $this->makeTwoPeriods();

        $this->asArray()->shouldReturn([$period1, $period2]);
    }

    public function it_sorts_in_ascending_order_of_start_date()
    {
        list($period1, $period2) = $this->makeTwoPeriods();

        $this->sort();
        $this->asArray()->shouldReturn([$period2, $period1]);
    }

    public function it_constructs_from_array_of_Periods(Period $period)
    {
        $this->beConstructedWith([$period->getWrappedObject(), $period->getWrappedObject()]);
        $this->asArray()->shouldReturn([$period, $period]);
    }

    public function it_splits_a_period_by_contents()
    {
        $masterPeriod = Period::fromStrings('2014-01-01', '2014-12-31');
        $period1      = Period::fromStrings('2014-02-01', '2014-02-28');
        $period2      = Period::fromStrings('2014-06-01', '2014-06-30');
        $this->beConstructedWith([$period1, $period2]);

        $this->splitPeriod($masterPeriod)->
            shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-01-31'),
                        $period1,
                        Period::fromStrings('2014-03-01', '2014-05-31'),
                        $period2,
                        Period::fromStrings('2014-07-01', '2014-12-31')
                    ]
                )
            );
    }

    /**
     * @return array
     */
    private function makeTwoPeriods()
    {
        $period1 = Period::fromStrings('2014-12-01', '2014-12-31');
        $period2 = Period::fromStrings('2014-01-01', '2014-01-31');

        $this->add($period1);
        $this->add($period2);

        return array($period1, $period2);
    }

    public function it_throws_if_adding_a_different_type_of_Period()
    {
        $period2 = Period::fromStrings('2014-01-01', '2014-02-01');
        $period1 = DummyPeriod::fromStrings('2014-01-01', '2014-02-01');
        $this->add($period1);
        $this->shouldThrow(
            PeriodTypeMismatchException::gotTypeButExpectedType(
                get_class($period2),
                get_class($period1)
            )
        )
            ->during('add', [$period2]);
    }

    public function it_does_nothing_when_resolving_overlaps_if_no_overlaps()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-03-01', '2014-04-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->resolveOverlaps()->shouldReturnPeriodCollection(
            new PeriodCollection(
                [
                    Period::fromStrings('2014-01-01', '2014-02-14'),
                    Period::fromStrings('2014-03-01', '2014-04-01'),
                ]
            )
        );
    }

    public function it_retrieves_first()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-02-01', '2014-03-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->first()->shouldReturn($period1);
    }

    public function it_retrieves_last()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-02-01', '2014-03-01');
        $period3 = Period::fromStrings('2015-02-01', '2015-03-01');
        $this->beConstructedWith([$period1, $period2, $period3]);
        $this->last()->shouldReturn($period3);
    }

    public function it_resolves_overlaps()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-02-01', '2014-03-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->resolveOverlaps()->shouldReturnPeriodCollection(
            new PeriodCollection(
                [
                    Period::fromStrings('2014-01-01', '2014-01-31'),
                    Period::fromStrings('2014-02-01', '2014-02-14'),
                    Period::fromStrings('2014-02-15', '2014-03-01')
                ]
            )
        );
    }

    public function it_merges_adjacent_periods_when_resolving_overlaps()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-02-15', '2014-03-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->resolveOverlaps()->shouldReturnPeriodCollection(
            new PeriodCollection(
                [
                    Period::fromStrings('2014-01-01', '2014-03-01')
                ]
            )
        );
    }

    public function it_combines_equal_periods_when_resolving_overlaps()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-02-14');
        $period2 = Period::fromStrings('2014-03-01', '2014-03-14');
        $period3 = Period::fromStrings('2014-03-01', '2014-03-14');
        $this->beConstructedWith([$period1, $period2, $period3]);
        $this->resolveOverlaps()->shouldReturnPeriodCollection(
            new PeriodCollection(
                [
                    Period::fromStrings('2014-01-01', '2014-02-14'),
                    Period::fromStrings('2014-03-01', '2014-03-14')
                ]
            )
        );
    }

    public function it_excludes_a_period()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-01-14');
        $period2 = Period::fromStrings('2014-02-01', '2014-03-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->exclude(Period::fromStrings('2014-01-07', '2014-02-07'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-01-06'),
                        Period::fromStrings('2014-02-08', '2014-03-01')
                    ]
                )
            );
    }

    public function it_does_not_exclude_an_out_of_range_period()
    {
        $period1 = Period::fromStrings('2014-01-01', '2014-01-14');
        $period2 = Period::fromStrings('2014-02-01', '2014-03-01');
        $this->beConstructedWith([$period1, $period2]);
        $this->exclude(Period::fromStrings('2015-01-07', '2015-02-07'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        $period1,
                        $period2
                    ]
                )
            );
    }

    public function getMatchers()
    {
        return ['returnPeriodCollection' => Matchers::returnPeriodCollection()];
    }
}

class DummyPeriod extends Period
{
    public function test()
    {

    }
}
