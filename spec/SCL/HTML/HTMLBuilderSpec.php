<?php

namespace spec\SCL\HTML;

use SCL\HTML\Exception\MismatchedTagException;
use SCL\HTML\Exception\TagStackException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HTMLBuilderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SCL\HTML\HTMLBuilder');
    }

    public function it_throws_when_popping_an_empty_stack()
    {
        $this->shouldThrow(new TagStackException('The tag stack is empty'))
            ->duringPopTag();
    }

    public function it_throws_when_the_popped_tag_does_not_match()
    {
        $this->pushTag('head', []);
        $this->shouldThrow(new MismatchedTagException("Expected tag 'body', but got 'head'"))
            ->duringPopTag('body');
    }
}
