<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidPeriodException;

class Period
{
    /** @var Date */
    private $start;

    /** @var Date */
    private $end;

    /**
     * @param string $start
     * @param string $end
     *
     * @return Period
     */
    public static function fromStrings($start, $end)
    {
        return new static(Date::fromString($start), Date::fromString($end));
    }

    /** @throws InvalidPeriodException */
    public function __construct(Date $start, Date $end)
    {
        if ($end->isBefore($start)) {
            throw InvalidPeriodException::endIsBeforeStart((string)$start, (string)$end);
        }

        $this->start = $start;
        $this->end   = $end;
    }

    /** @return Date */
    public function getStart()
    {
        return $this->start;
    }

    /** @return Date */
    public function getEnd()
    {
        return $this->end;
    }

    /** @return boolean */
    public function isOverlappingWith(self $that)
    {
        return $this->isCompletelyOverlappedBy($that)
        || $this->isCompletelyOverlapping($that)
        || $this->hasStartOverlappedBy($that)
        || $this->hasEndOverlappedBy($that);
    }

    public function __toString()
    {
        return $this->start . ' to ' . $this->end;
    }

    /** @return boolean */
    public function isCompletelyOverlappedBy(self $that)
    {
        return $this->start->isAfterOrSameAs($that->getStart())
        && $this->end->isBeforeOrSameAs($that->getEnd());
    }

    /** @return boolean */
    private function isCompletelyOverlapping(self $that)
    {
        return $that->getStart()->isAfterOrSameAs($this->start)
        && $that->getEnd()->isBeforeOrSameAs($this->end);
    }

    /** @return boolean */
    private function hasStartOverlappedBy(self $that)
    {
        return $this->start->isAfterOrSameAs($that->getStart())
        && $this->start->isBeforeOrSameAs($that->getEnd());
    }

    /** @return boolean */
    private function hasEndOverlappedBy(self $that)
    {
        return $this->end->isAfterOrSameAs($that->getStart())
        && $this->end->isBeforeOrSameAs($that->getEnd());
    }

    /** @return PeriodCollection */
    public function splitByDate(Date $date)
    {
        if ($this->start->isBefore($date) && $this->end->isAfter($date)) {
            return new PeriodCollection(
                [
                    $this->createLike($this->start, $date->yesterday()),
                    $this->createLike($date, $this->end)
                ]
            );
        }
        if ($this->start->isSameAs($date) && $this->end->isAfter($date)) {
            return new PeriodCollection(
                [
                    $this->createLike($date, $this->end)
                ]
            );
        }

        return new PeriodCollection([$this]);
    }


    /** @return PeriodCollection */
    public function splitByPeriod(Period $period)
    {
        if ($this->exactlyMatches($period)) {
            return new PeriodCollection([$this->selectPeriod($period)]);
        }
        if ($this->isCompletelyContaining($period)) {
            return $this->splitIntoThree($period);
        }

        if ($this->hasStartOverlappedBy($period)) {
            return $this->splitAtStart($period);
        }

        if ($this->hasEndOverlappedBy($period)) {
            return $this->splitAtEnd($period);
        }

        return new PeriodCollection([$this]);
    }

    /**
     * @param Period $period
     * @return PeriodCollection
     */
    protected function splitIntoThree(Period $period)
    {
        return new PeriodCollection(
            [
                $this->createLike($this->start, $period->getStart()->yesterday()),
                $period,
                $this->createLike($period->getEnd()->tomorrow(), $this->end)
            ]
        );
    }

    /**
     * @param Period $period
     * @return PeriodCollection
     */
    protected function splitAtStart(Period $period)
    { //@todo check this is right, or should it do what splitAtEnd does?
        $periods = [
            $this->createLike($this->start, $period->getEnd()),
        ];
        if ($period->end->isBefore($this->getEnd())) {
            $periods[] = $this->createLike($period->getEnd()->tomorrow(), $this->end);
        }

        return new PeriodCollection(
            $periods
        );
    }

    /**
     * @param Period $period
     * @return PeriodCollection
     */
    protected function splitAtEnd(Period $period)
    {
        $periods = [
            $this->createLike($this->start, $period->getStart()->yesterday()),
            $this->createOverlap($period->getStart(), $this->end, $period),
        ];
        if ($this->end->isBefore($period->getEnd())) {
            $periods[] = $this->createLike($this->end->tomorrow(), $period->getEnd());
        }

        return new PeriodCollection(
            $periods
        );
    }

    /**
     * @param Period $period
     * @return bool
     */
    private function isCompletelyContaining(Period $period)
    {
        return $this->isCompletelyOverlapping($period)
        && $this->getStart()->isBefore($period->getStart())
        && $this->getEnd()->isAfter($period->getEnd());
    }

    public function createLike(Date $start, Date $end)
    {
        return new static($start, $end);
    }

    public function createOverlap(Date $start, Date $end, Period $period)
    {
        return $this->createLike($start, $end);
    }


    public function contains(Date $date)
    {
        return $date->isAfterOrSameAs($this->start)
        && $date->isBeforeOrSameAs($this->end);
    }

    private function exactlyMatches(Period $period)
    {
        return $this->getStart()->isSameAs($period->getStart()) && $this->getEnd()->isSameAs($period->getEnd());
    }

    public function selectPeriod(Period $period)
    {
        return $this;
    }

    public function canMergeWith(Period $next)
    {
        return $this->exactlyMatches($next);
    }

    public function isContiguousWith(Period $period)
    {
        return $this->getEnd()->tomorrow()->isSameAs($period->getStart())
        || $this->getStart()->yesterday()->isSameAs($period->getEnd());
    }

    public function coalesce(Period $period)
    {
        if ($this->getStart()->isBefore($period->getStart())) {
            $result = $this->createLike($this->getStart(), $period->getEnd());
        } else {
            $result = $this->createLike($period->getStart(), $this->getEnd());
        }

        return $result;
    }

    public function canCoalesce($other)
    {
        return true;
    }

    public function exclude(Period $period)
    {
        if (!$this->isOverlappingWith($period)) {
            return new PeriodCollection([$this]);
        }
        $periods = [];
        if ($this->exactlyMatches($period)) {
            return new PeriodCollection($periods);
        }
        $temp = $this->splitByDate($period->getStart());
        if ($temp->count() > 1) {
            $periods[] = $temp->first();
        }
        $temp = $this->splitByDate($period->getEnd()->tomorrow());
        if ($temp->count() > 1) {
            $periods[] = $temp->last();
        };

        return new PeriodCollection($periods);
    }

    public function intersectWith(PeriodCollection $periods)
    {
        $results = [];
        foreach ($periods->asArray() as $period) {
            $results = $this->intersectWithPeriod($period, $results);
        }

        return new PeriodCollection($results);
    }

    /** @return array */
    private function intersectWithPeriod(Period $period, array $results)
    {
        if (!$this->isOverlappingWith($period)) {
            return $results;
        }

        if ($period->isCompletelyOverlapping($this)) {
            $results[] = $this;

            return $results;
        }

        if ($period->getEnd()->isAfter($this->getEnd())) {
            $results[] = new Period($period->getStart(), $this->getEnd());

            return $results;
        }

        if ($this->periodOverlapsAtStartOnly($period)) {
            $results[] = new Period($this->getStart(), $period->getEnd());

            return $results;
        }

        $results[] = $period;

        return $results;
    }

    /**
     * @param Period $period
     * @return bool
     */
    private function periodOverlapsAtStartOnly(Period $period)
    {
        return $period->getStart()->isBefore($this->getStart())
        && $period->getEnd()->isBeforeOrSameAs($this->getEnd());
    }

    public function getLength()
    {
        return $this->start->daysUntil($this->end);
    }
}
