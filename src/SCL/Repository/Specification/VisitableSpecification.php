<?php

namespace SCL\Repository\Specification;

class VisitableSpecification extends Specification
{
    private static $namespace = __NAMESPACE__;

    public function accept(Visitor $visitor)
    {
        return $visitor->{$this->getVisitMethodName($visitor, get_class($this))}($this);
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private function makeVisitMethodName($className)
    {
        return 'visit' . str_replace('\\', '', str_replace(self::$namespace . '\\', '', $className));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private function getVisitMethodName(Visitor $visitor, $className)
    {
        $methodName = $this->makeVisitMethodName($className);

        return $this->isAMethodOf($methodName, $visitor) ? $methodName : 'defaultVisit';
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    private function isAMethodOf($methodName, Visitor $visitor)
    {
        return in_array($methodName, get_class_methods($visitor));
    }
}
