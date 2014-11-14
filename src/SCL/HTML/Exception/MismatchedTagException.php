<?php
namespace SCL\HTML\Exception;

use SCL\Exception\ExceptionFactory;

class MismatchedTagException extends \LogicException
{
    use ExceptionFactory;

    /**
     * @param string $expected
     * @param string $actual
     *
     * @return MismatchedTagException
     */
    public static function wrongTag($expected, $actual)
    {
        return self::create("Expected tag '%s', but got '%s'", [$expected, $actual]);
    }
}
