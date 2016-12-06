<?php

namespace spec\SCL\Repository\Value;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SCL\Repository\Exception\NullIdentityException;
use SCL\Repository\Value\Identity;
use SCL\Repository\Value\RealIdentity;

class NullIdentitySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Identity::class);
    }

    public function it_has_no_id()
    {
        $this->__toString()->shouldReturn('');
    }

    public function it_has_no_value()
    {
        $this->shouldThrow(NullIdentityException::class)->duringGetValue();
    }
}
