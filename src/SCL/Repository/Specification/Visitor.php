<?php

namespace SCL\Repository\Specification;

interface Visitor
{
    public function defaultVisit(Specification $spec);

    /** @return string */
    public function getClassName();
}
