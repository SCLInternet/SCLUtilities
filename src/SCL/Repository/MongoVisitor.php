<?php

namespace SCL\Repository;

use SCL\Repository\Specification\Specification;
use SCL\Repository\Specification\VisitableSpecification;
use SCL\Repository\Specification\Visitor;

class MongoVisitor implements Visitor
{
    /** @var array */
    protected $query;

    /** @var Specification[] */
    protected $specifications;

    public function __construct()
    {
        $this->query          = [];
        $this->specifications = [];
    }

    /** @return array */
    public function getQuery()
    {
        return $this->query;
    }

    public function defaultVisit(Specification $spec)
    {
        $this->addUnhandledSpecification($spec);
    }

    /** @param Specification[] $specifications */
    public function processSpecs(array $specifications)
    {
        foreach ($specifications as $spec) {
            $this->handleSpec($spec);
        }
    }

    public function addToQuery(array $partial)
    {
        $this->query = array_merge($this->query, $partial);
    }

    /** @return string */
    public function getClassName()
    {
        return __CLASS__;
    }


    protected function addUnhandledSpecification(Specification $spec)
    {
        $this->specifications[] = $spec;
    }


    protected function handleSpec(Specification $spec)
    {
        if ($spec instanceof VisitableSpecification) {
            $spec->accept($this);
        } else {
            $this->addUnhandledSpecification($spec);
        }
    }
}
 