<?php
declare(strict_types = 1);

namespace SCL\Repository\Value;

use SCL\Repository\Exception\NullIdentityException;

final class NullIdentity implements Identity
{
    public function __construct()
    {
    }

    public function getValue() : string
    {
        return '';
//        throw NullIdentityException::methodCall('getValue');
    }

    public function __toString()
    {
        return '';
    }

    public function isSameAs(Identity $other)
    {
        return false;
    }
}
