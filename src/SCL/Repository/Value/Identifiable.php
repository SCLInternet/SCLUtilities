<?php

namespace SCL\Repository\Value;

interface Identifiable
{
    public function setId(Identity $id);

    /** @return Identity */
    public function getId();
}
