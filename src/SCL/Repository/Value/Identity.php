<?php

namespace SCL\Repository\Value;

use SCL\Repository\Exception\EmptyIdentityException;

class Identity
{
    /** @var string */
    private $id;

    /** @param string $id */
    public function __construct($id)
    {
        if (!$id) {
            throw EmptyIdentityException::emptyIdentity();
        }
        $this->id = (string)$id;
    }

    /** @return string */
    public function getValue()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}
