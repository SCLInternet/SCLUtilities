<?php

namespace SCL\Repository\Value;

use SCL\Repository\Exception\NullIdentityException;

final class NullIdentity implements Identity
{
    public function __construct()
    {
    }

    /** @return string */
    public function getValue()
    {
        throw NullIdentityException::methodCall('getValue');
    }

    public function __toString()
    {
        return '';
    }
}
