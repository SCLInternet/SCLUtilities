<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\PeriodTypeMismatchException;

class PeriodCollection
{
    /** @var Period[] */
    private $periods = [];

    /** @var string */
    private $baseType;

    /** @var Period */
    private $first;

    public function __construct(array $periods = null)
    {
        if (!$periods) {
            return;
        }
        foreach ($periods as $period) {
            $this->add($period);
        }
    }

    /** @return int */
    public function count()
    {
        return count($this->periods);
    }

    public function add(Period $period)
    {
        $theType = get_class($period);
        if (empty($this->periods)) {
            $this->baseType = $theType;
            $this->first    = $period;
        }
        if ($theType !== $this->baseType) {
            throw PeriodTypeMismatchException::gotTypeButExpectedType($theType, $this->baseType);
        }
        $this->periods[] = $period;
    }

    public function asArray()
    {
        return $this->periods;
    }

    public function splitPeriod(Period $period)
    {
        $currentPeriod = $period;
        $result        = new self();
        foreach ($this->periods as $aPeriod) {
            $currentPeriod = $this->splitByPeriod($currentPeriod, $aPeriod, $result);
        }
        if ($currentPeriod) {
            $result->add($currentPeriod);
        }

        return $result;
    }

    /** @return Period */
    private function splitByPeriod(Period $currentPeriod, Period $aPeriod, PeriodCollection $result)
    {
        /** @var PeriodCollection $periods */
        $periods = $currentPeriod->splitByPeriod($aPeriod);
        $count   = $periods->count();
        if ($count === 1) {
            return $currentPeriod;
        } else {
            for ($idx = 0; $idx < $count - 1; $idx++) {
                $result->add($periods->at($idx));
            }
            $currentPeriod = $periods->last();

            return $currentPeriod;
        }
    }

    /** @return Period */
    public function first()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->at(0);
    }

    /** @return Period */
    public function last()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->at($this->count() - 1);
    }

    /**
     * @param int $index
     *
     * @return Period
     */
    public function at($index)
    {
        $this->assertIndexValid($index);

        return $this->periods[$index];
    }

    public function sort()
    {
        usort(
            $this->periods,
            function (Period $period1, Period $period2) {
                $date1 = $period1->getStart();
                $date2 = $period2->getStart();
                if ($date1->isBefore($date2)) {
                    return -1;
                } elseif ($date1->isAfter($date2)) {
                    return 1;
                }

                return 0;
            }
        );
    }

    public function __toString()
    {
        return "[\n\t" .
        implode(
            ",\n\t",
            $this->asStringArray()
        )
        . "\n]";
    }

    /**
     * @param $index
     * @throws \OutOfBoundsException
     */
    private function assertIndexValid($index)
    {
        if ($index >= $this->count()) {
            throw new \OutOfBoundsException("Index out of bounds $index");
        }
    }

    /** @return bool */
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    public function resolveOverlaps()
    {
        if ($this->count() <= 1) {
            return $this;
        }
        $this->sort();
        $temp = new PeriodCollection();
        $last = null;
        for ($idx = 0; $idx < $this->count() - 1; $idx++) {
            $first = $this->at($idx);
            $next  = $this->at($idx + 1);
            if ($first->canMergeWith($next)) {
                $this->add($first->selectPeriod($next));
                $idx++;
            } elseif ($first->isContiguousWith($next) && $first->canCoalesce($next)) {
                $this->add($first->coalesce($next));
                $idx++;
            } elseif ($first->isOverlappingWith($next)) {
                $periods                 = $first->splitByPeriod($next);
                $last                    = $temp->appendAllButLast($periods);
                $this->periods[$idx + 1] = $last;
            } else {
                $temp->add($first);
            }
        }
        if ($this->last()) {
            $temp->add($this->last());
        }

        return $temp;
    }

    private function appendAllButLast(PeriodCollection $collection)
    {
        if ($collection->isEmpty()) {
            return null;
        }
        if ($collection->count() == 1) {
            $this->add($collection->first());

            return null;
        }
        for ($idx = 0; $idx < $collection->count() - 1; $idx++) {
            $this->add($collection->at($idx));
        }

        return $collection->last();
    }

    /**
     * @return array
     */
    public function asStringArray()
    {
        return array_map(
            function ($value) {
                return (string)$value;
            },
            $this->periods
        );
    }

    public function exclude(Period $other)
    {
        $periods = array_reduce(
            $this->periods,
            function ($periods, Period $period) use ($other) {
                $partial = $period->exclude($other);

                return array_merge($periods, $partial->asArray());
            },
            []
        );

        return new PeriodCollection($periods);
    }
}
