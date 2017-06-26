<?php
declare(strict_types = 1);

namespace SCL\Repository\Value;

use SCL\Repository\Exception\EmptyIdentityException;

trait IdentityTrait
{
    /** @var string */
    private $id;

    /** @param string $id */
    public function __construct(string $id)
    {
        if (!$id) {
            throw EmptyIdentityException::emptyIdentity();
        }
        $this->id = (string)$id;
    }

    public function getValue() : string
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}