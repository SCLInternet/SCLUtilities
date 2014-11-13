<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidDateException;
use SCL\DatesAndTimes\Exception\InvalidStringFormatException;
use DateTimeImmutable;

class Date
{
    const STRING_REGEX = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/';

    /** @var int */
    private $day;

    /** @var Month */
    private $month;

    /** @var Year */
    private $year;

    public static function fromString($string)
    {
        if (!preg_match(self::STRING_REGEX, $string, $matches)) {
            throw InvalidStringFormatException::invalidFormat('Date', 'YYYY-MM-DD', $string);
        }

        return new self(new Year($matches[1]), new Month($matches[2]), $matches[3]);
    }

    /**
     * @param int $day
     *
     * @throws InvalidDateException
     */
    public function __construct(Year $year, Month $month, $day)
    {
        $this->day   = (int) $day;
        $this->month = $month;
        $this->year  = $year;

        if ((string) $this !== $this->getPhpDateTime()->format('Y-m-d')) {
            throw InvalidDateException::invalidDate(
                $this->year->getValue(),
                $this->month->getValue(),
                $this->day
            );
        }
    }

    /** @return int */
    public function getDay()
    {
        return $this->day;
    }

    /** @return Month */
    public function getMonth()
    {
        return $this->month;
    }

    /** @return Year */
    public function getYear()
    {
        return $this->year;
    }

    /** @return boolean */
    public function isBefore(self $that)
    {
        $diff = $that->getPhpDateTime()->diff($this->getPhpDateTime());

        return (bool) $diff->invert;
    }

    /** @return boolean */
    public function isBeforeOrSameAs(self $that)
    {
        $diff = $this->getPhpDateTime()->diff($that->getPhpDateTime());

        return (bool) !$diff->invert;
    }

    /** @return boolean */
    public function isAfter(self $that)
    {
        $diff = $this->getPhpDateTime()->diff($that->getPhpDateTime());

        return (bool) $diff->invert;
    }

    /** @return boolean */
    public function isAfterOrSameAs(self $that)
    {
        $diff = $that->getPhpDateTime()->diff($this->getPhpDateTime());

        return (bool) !$diff->invert;
    }

    public function __toString()
    {
        return sprintf(
            '%04d-%02d-%02d',
            $this->year->getValue(),
            $this->month->getValue(),
            $this->day
        );
    }

    public function yesterday()
    {
        return $this->modify('yesterday');
    }

    public function tomorrow()
    {
        return $this->modify('tomorrow');
    }

    public function nextMonth()
    {
        return $this->modify('first day of next month');
    }

    public function nextWeek()
    {
        return $this->modify('+1 week');
    }

    /** @return DateTimeImmutable */
    protected function getPhpDateTime()
    {
        return new DateTimeImmutable((string) $this);
    }

    /**
     * @param string $modifier see PHP DateTime modify()
     *
     * @return Date
     */
    public function modify($modifier)
    {
        $yesterday = $this->getPhpDateTime()->modify($modifier);

        return Date::fromString($yesterday->format('Y-m-d'));
    }

    public function isSameAs(self $other)
    {
        return $this->getPhpDateTime() == $other->getPhpDateTime();
    }


    public function formatted($format)
    {
        return $this->getPhpDateTime()->format($format);
    }
}
