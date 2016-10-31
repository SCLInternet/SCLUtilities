<?php
namespace SCL\Repository\Exception;

use SCL\Exception\ExceptionFactory;

class NullIdentityException extends \LogicException
{
    use ExceptionFactory;

    public static function methodCall($methodName)
    {
        return self::create("Attempt to call a method '$methodName' on a NullIdentity");
    }
}
