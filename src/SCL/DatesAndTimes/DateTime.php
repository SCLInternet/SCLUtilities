<?php

namespace SCL\DatesAndTimes;

use SCL\DatesAndTimes\Exception\InvalidStringFormatException;

class DateTime
{
    /** @var Date */
    private $date;

    /** @var Time */
    private $time;

    public function __construct(Date $date, Time $time)
    {
        $this->date = $date;
        $this->time = $time;
    }

    /**
     * @param $string
     * @throws InvalidStringFormatException
     */
    private static function reportInvalidString($string)
    {
        throw InvalidStringFormatException::invalidFormat('DateTime', 'YYYY-MM-DD HH:MM:SS', $string);
    }

    /**
     * @param $string
     * @return array
     */
    private static function getParts($string)
    {
        $parts = explode(' ', $string);

        if (count($parts) != 2) {
            self::reportInvalidString($string);

            return $parts;
        }

        return $parts;
    }

    /**
     * @param string $string
     *
     * @return Date
     */
    private static function makeDate($string, array $parts)
    {
        $date = null;

        try {
            $date = Date::fromString($parts[0]);
        } catch (\Exception $e) {
            self::reportInvalidString($string);
        }

        return $date;
    }

    /**
     * @param string $string
     *
     * @return Time
     */
    private static function makeTime($string, array $parts)
    {
        $time = null;

        try {
            $time = Time::fromString($parts[1]);
        } catch (\Exception $e) {
            self::reportInvalidString($string);
        }

        return $time;
    }

    public function __toString()
    {
        return "{$this->date} {$this->time}";
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getTime()
    {
        return $this->time;
    }

    public static function fromString($string)
    {
        $parts = self::getParts($string);

        return new self(self::makeDate($string, $parts), self::makeTime($string, $parts));
    }
}
