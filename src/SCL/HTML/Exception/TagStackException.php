<?php
namespace SCL\HTML\Exception;

use SCL\Exception\ExceptionFactory;

class TagStackException extends \LogicException
{
    use ExceptionFactory;

    /** @return TagStackException */
    public static function popOnEmpty($expectedTagName = null)
    {
        return self::create("The tag stack is empty" . ($expectedTagName ? " $expectedTagName" : ""));
    }
}
