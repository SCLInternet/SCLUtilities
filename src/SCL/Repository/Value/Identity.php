<?php
namespace SCL\Repository\Value;

interface Identity
{
    /** @return string */
    public function getValue();

    public function __toString();
}