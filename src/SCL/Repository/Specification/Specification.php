<?php

namespace SCL\Repository\Specification;

abstract class Specification
{
    /**
     * @param object $target
     *
     * @return bool
     */
    public function isSatisfiedBy($target)
    {
        if (!method_exists($this, 'isSatisfiedByRule')) {
            throw new \LogicException(
                'Method isSatisfiedByRule() is not defined in ' . get_class($this)
            );
        }

        return $this->isSatisfiedByRule($target);
    }

    /*
     * This is commented out so we can take advantage of PHP's type
     * hinting in child classes.
     *
     * @return bool
     */
    //abstract protected function isSatisfiedByRule(Identifiable $target);
}
