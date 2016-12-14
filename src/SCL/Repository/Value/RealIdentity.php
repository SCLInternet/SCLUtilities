<?php

namespace SCL\Repository\Value;

final class RealIdentity implements Identity
{
    use IdentityTrait;

    public function isSameAs(Identity $other)
    {
        return $this->id === $other->getValue();
    }
}
