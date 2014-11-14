<?php

namespace spec\SCL\DatesAndTimes;

use SCL\PHPSpec\Matchers;
use SCL\DatesAndTimes\Exception\InvalidPeriodException;
use SCL\DatesAndTimes\Date;
use SCL\DatesAndTimes\Period;
use SCL\DatesAndTimes\PeriodCollection;
use PhpSpec\ObjectBehavior;

class PeriodSpec extends ObjectBehavior
{
    public function it_throws_if_start_is_after_end()
    {
        $start = Date::fromString('2014-05-15');
        $end   = Date::fromString('2014-05-14');

        $this->shouldThrow(InvalidPeriodException::endIsBeforeStart($start, $end))
            ->during('__construct', [$start, $end]);
    }

    public function it_returns_start_and_end_dates()
    {
        $start = Date::fromString('2014-05-15');
        $end   = Date::fromString('2014-05-16');

        $this->beConstructedWith($start, $end);

        $this->getStart()->shouldReturn($start);
        $this->getEnd()->shouldReturn($end);
    }

    public function it_converts_to_string()
    {
        $start = Date::fromString('2014-05-15');
        $end   = Date::fromString('2014-05-16');

        $this->beConstructedWith($start, $end);

        $this->__toString()->shouldReturn('2014-05-15 to 2014-05-16');
    }

    public function it_can_be_constructed_from_2_date_strings()
    {
        $this->keepPhpSpecHappyForStaticCalls();

        $period = $this::fromStrings('2014-05-21', '2014-05-30');

        $period->__toString()->shouldReturn('2014-05-21 to 2014-05-30');
    }

    public function it_checks_overlap_for_period_which_have_no_overlaps()
    {
        $this->shouldNotOverlapForDates(
            '2014-01-01', // start a
            '2014-05-01', // end a
            '2013-01-01', // start b
            '2013-05-01' // end b
        );
    }

    public function it_checks_overlap_for_period_which_completely_covers()
    {
        $this->shouldOverlapForDates(
            '2014-01-01', // start a
            '2014-05-01', // end a
            '2013-01-01', // start b
            '2015-01-01' // end b
        );
    }

    public function it_checks_overlap_for_period_which_is_completely_overlapped()
    {
        $this->shouldOverlapForDates(
            '2013-01-01', // start a
            '2015-01-01', // end a
            '2014-01-01', // start b
            '2014-10-01' // end b
        );
    }

    public function it_checks_overlap_for_period_which_overlaps_start()
    {
        $this->shouldOverlapForDates(
            '2014-02-01', // start a
            '2014-06-01', // end a
            '2014-01-01', // start b
            '2014-04-01' // end b
        );
    }

    public function it_checks_overlap_for_period_which_overlaps_end()
    {
        $this->shouldOverlapForDates(
            '2014-01-01', // start a
            '2014-06-01', // end a
            '2014-04-01', // start b
            '2014-08-01' // end b
        );
    }

    public function it_checks_overlap_for_period_which_are_the_same()
    {
        $this->shouldOverlapForDates(
            '2014-01-01', // start a
            '2014-05-01', // end a
            '2014-01-01', // start b
            '2014-05-01' // end b
        );
    }

    public function it_splits_on_an_inclusive_date()
    {
        $this->constructWithYear2014();
        $this->splitByDate(Date::fromString('2014-06-01'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-05-31'),
                        Period::fromStrings('2014-06-01', '2014-12-31')
                    ]
                )
            );
    }

    public function it_does_not_split_on_a_date_outwith_the_Period()
    {
        $this->constructWithYear2014();
        $this->splitByDate(Date::fromString('2013-06-01'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-12-31')
                    ]
                )
            );
    }

    public function it_splits_by_period_in_the_middle()
    {
        $this->constructWithYear2014();
        $this->splitByPeriod(Period::fromStrings('2014-06-01', '2014-06-30'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-05-31'),
                        Period::fromStrings('2014-06-01', '2014-06-30'),
                        Period::fromStrings('2014-07-01', '2014-12-31')
                    ]
                )
            );

    }

    public function it_splits_by_period_overlapping_start()
    {
        $this->constructWithYear2014();
        $this->splitByPeriod(Period::fromStrings('2014-01-01', '2014-06-30'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-06-30'),
                        Period::fromStrings('2014-07-01', '2014-12-31')
                    ]
                )
            );
    }

    public function it_splits_by_period_overlapping_end()
    {
        $this->constructWithYear2014();
        $this->splitByPeriod(Period::fromStrings('2014-06-01', '2015-02-15'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-05-31'),
                        Period::fromStrings('2014-06-01', '2014-12-31'),
                        Period::fromStrings('2015-01-01', '2015-02-15')
                    ]
                )
            );
    }

    public function it_returns_the_original_period_if_no_overlaps()
    {
        $this->constructWithYear2014();
        $this->splitByPeriod(Period::fromStrings('2013-06-01', '2013-12-31'))
            ->shouldReturnPeriodCollection(
                new PeriodCollection([$this->getWrappedObject()])
            );
    }

    public function it_contains_the_start_date()
    {
        $this->constructWithYear2014();
        $this->contains(Date::fromString('2014-01-01'))->shouldReturn(true);
    }

    public function it_contains_the_end_date()
    {
        $this->constructWithYear2014();
        $this->contains(Date::fromString('2014-12-31'))->shouldReturn(true);
    }

    public function it_does_not_contain_the_day_after_the_end_date()
    {
        $this->constructWithYear2014();
        $this->contains(Date::fromString('2015-01-01'))->shouldReturn(false);
    }

    public function it_does_not_contain_the_day_before_the_start_date()
    {
        $this->constructWithYear2014();
        $this->contains(Date::fromString('2013-12-31'))->shouldReturn(false);
    }

    public function it_checks_for_contiguousness()
    {
        $this->constructWithYear2014();
        $this->shouldBeContiguousWith(Period::fromStrings('2013-12-12', '2013-12-31'));
        $this->shouldBeContiguousWith(Period::fromStrings('2015-01-01', '2015-12-31'));
        $this->shouldNotBeContiguousWith(Period::fromStrings('2015-01-02', '2015-12-31'));
    }

    private function shouldNotOverlapForDates($thisStart, $thisEnd, $thatStart, $thatEnd)
    {
        $this->beConstructedWith(Date::fromString($thisStart), Date::fromString($thisEnd));

        $other = Period::fromStrings($thatStart, $thatEnd);

        $this->shouldNotBeOverlappingWith($other);
    }

    public function it_excludes_a_period()
    {
        $this->constructWithYear2014();

        $other = Period::fromStrings('2013-01-01', '2015-12-31');

        $this->exclude($other)->shouldReturnPeriodCollection(new PeriodCollection([]));
    }

    public function it_excludes_a_period_at_the_start()
    {
        $this->constructWithYear2014();

        $other = Period::fromStrings('2013-12-01', '2014-06-06');

        $this->exclude($other)->shouldReturnPeriodCollection(
            new PeriodCollection([
                Period::fromStrings('2014-06-07', '2014-12-31')
            ])
        );
    }

    public function it_excludes_a_period_at_the_end()
    {
        $this->constructWithYear2014();

        $other = Period::fromStrings('2014-06-06', '2015-12-31');

        $this->exclude($other)->shouldReturnPeriodCollection(
            new PeriodCollection([
                Period::fromStrings('2014-01-01', '2014-06-05')
            ])
        );
    }

    public function it_excludes_a_period_in_the_middle()
    {
        $this->constructWithYear2014();

        $other = Period::fromStrings('2014-06-06', '2014-07-07');

        $this->exclude($other)->shouldReturnPeriodCollection(
            new PeriodCollection([
                Period::fromStrings('2014-01-01', '2014-06-05'),
                Period::fromStrings('2014-07-08', '2014-12-31')
            ])
        );
    }

    public function it_does_not_exclude_an_out_of_range_period()
    {
        $this->constructWithYear2014();

        $other = Period::fromStrings('2015-06-06', '2015-07-07');

        $this->exclude($other)->shouldReturnPeriodCollection(
            new PeriodCollection([
                Period::fromStrings('2014-01-01', '2014-12-31')
            ])
        );
    }

    public function it_creates_like()
    {
        $this->constructWithYear2014();
        $this->createLike(Date::fromString('2014-01-02'), Date::fromString("2014-12-30"))
            ->shouldBeAnInstanceOf('SCL\DatesAndTimes\Period');
    }

    public function it_intersects_with_a_contained_period()
    {
        $this->constructWithYear2014();
        $this->intersectWith(new PeriodCollection([Period::fromStrings('2014-06-10', '2014-06-30')]))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-06-10', '2014-06-30')
                    ]
                )
            );
    }

    public function it_intersects_with_a_period_that_overlaps_at_start()
    {
        $this->constructWithYear2014();
        $this->intersectWith(new PeriodCollection([Period::fromStrings('2013-06-01', '2014-01-31')]))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-01-31')
                    ]
                )
            );
    }

    public function it_intersects_with_a_period_that_overlaps_at_end()
    {
        $this->constructWithYear2014();
        $this->intersectWith(new PeriodCollection([Period::fromStrings('2014-06-01', '2015-01-31')]))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-06-01', '2014-12-31')
                    ]
                )
            );
    }

    public function it_does_not_intersect_with_a_non_overlapping_period()
    {
        $this->constructWithYear2014();
        $this->intersectWith(new PeriodCollection([Period::fromStrings('2013-06-01', '2013-07-31')]))
            ->shouldReturnPeriodCollection(
                new PeriodCollection([])
            );
    }

    public function it_intersects_with_a_fully_overlapping_period()
    {
        $this->constructWithYear2014();
        $this->intersectWith(new PeriodCollection([Period::fromStrings('2013-12-01', '2015-01-01')]))
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-01-01', '2014-12-31')
                    ]
                )
            );
    }

    public function it_intersects_with_two_periods()
    {
        $this->constructWithYear2014();
        $this->intersectWith(
            new PeriodCollection(
                [
                    Period::fromStrings('2014-02-01', '2014-02-28'),
                    Period::fromStrings('2014-04-01', '2014-04-28')
                ]
            )
        )
            ->shouldReturnPeriodCollection(
                new PeriodCollection(
                    [
                        Period::fromStrings('2014-02-01', '2014-02-28'),
                        Period::fromStrings('2014-04-01', '2014-04-28')
                    ]
                )
            );
    }

    private function shouldOverlapForDates($thisStart, $thisEnd, $thatStart, $thatEnd)
    {
        $this->beConstructedWith(Date::fromString($thisStart), Date::fromString($thisEnd));

        $other = Period::fromStrings($thatStart, $thatEnd);

        $this->shouldBeOverlappingWith($other);
    }

    private function keepPhpSpecHappyForStaticCalls()
    {
        $this->beConstructedWith(
            Date::fromString('2015-03-01'),
            Date::fromString('2015-04-01')
        );
    }

    public function getMatchers()
    {
        return ['returnPeriodCollection' => Matchers::returnPeriodCollection()];
    }

    private function constructWithYear2014()
    {
        $this->beConstructedWith(Date::fromString('2014-01-01'), Date::fromString('2014-12-31'));
    }
}
