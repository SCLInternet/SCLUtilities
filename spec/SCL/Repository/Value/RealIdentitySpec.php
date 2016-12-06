<?php

namespace spec\SCL\Repository\Value;

use PhpSpec\ObjectBehavior;
use SCL\Repository\Exception\EmptyIdentityException;

class RealIdentitySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('idstring');
    }

    public function it_returns_value()
    {
        $this->getValue()->shouldReturn('idstring');
    }

    public function it_converts_to_string()
    {
        $this->beConstructedWith(123);

        $this->__toString()->shouldReturn('123');
    }

    public function it_throws_when_empty()
    {
        $this->shouldThrow(EmptyIdentityException::emptyIdentity())->during("__construct", [""]);
    }
}
