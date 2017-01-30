<?php
declare(strict_types = 1);

namespace SCL\Repository\Value;

interface Identity
{
    public function getValue() : string;

    public function __toString();

    public function isSameAs(Identity $other);
}