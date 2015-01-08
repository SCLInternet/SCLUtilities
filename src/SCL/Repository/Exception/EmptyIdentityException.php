<?php
namespace SCL\Repository\Exception;

use SCL\Exception\ExceptionFactory;

class EmptyIdentityException extends \LogicException
{
    use ExceptionFactory;

    public static function emptyIdentity()
    {
        return self::create("Identity is empty");
    }
}
