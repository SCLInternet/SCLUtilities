<?php

namespace spec\SCL\Repository\Specification;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VisitableSpecificationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SCL\Repository\Specification\Specification');
    }
}
