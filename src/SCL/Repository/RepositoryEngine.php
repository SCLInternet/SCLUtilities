<?php

namespace SCL\Repository;

use SCL\Repository\Value\RealIdentity;
use SCL\Repository\Value\Identity;
use SCL\Repository\Value\NullIdentity;
use MongoId;
use SCL\Repository\Exception\NoSuchEntityException;
use SCL\Repository\Specification\Specification;
use SCL\Repository\Specification\VisitableSpecification;
use SCL\Repository\Value\Identifiable;

trait RepositoryEngine
{
    /** @var Collection */
    protected $collection;

    /** @var string */
    protected $entityName = '';

    public function loadById(Identity $id)
    {
        return $this->loadOne(
            ['_id' => new MongoId((string)$id)],
            "No  {$this->entityName} with id = $id"
        );
    }

    /** @return object[] */
    public function loadAll()
    {
        return $this->loadMany([]);
    }

    /** @return object[] */
    public function loadBySpecification(Specification $specification, $debug=false)
    {
        return $this->loadBySpecificationWithQuery($specification, [], $debug);
    }

    /**
     * @return object
     *
     * @throw NoSuchEntityException
     */
    public function loadOneBySpecification(Specification $specification, $debug=false)
    {
        // @todo maybe should be a version that doesn't throw
        $entities = $this->loadBySpecificationWithQuery($specification, [], $debug);

        if (empty($entities)) {
            throw new NoSuchEntityException();
        }

        // @todo Assert there are no more than 1 result

        return reset($entities);
    }

    public function removeAll()
    {
        $this->collection->remove([]);
    }

    public function removeBySpecification(Specification $specification)
    {
        if (($specification instanceof VisitableSpecification)) {
            list($query, $extra) = $this->buildMongoQuery($specification);
            if (count($extra) == 0) {
                $this->collection->remove($query);

                return;
            }
        }

        foreach ($this->loadBySpecification($specification) as $entity) {
            $this->remove($entity);
        }

        return;
    }

    protected function applyRemove(Identifiable $entity)
    {
        $this->collection->remove(['_id' => new MongoId((string)$entity->getId())]);
    }

    /** @return object[] */
    protected function loadBySpecificationWithQuery(Specification $specification, array $query, $debug = false)
    {
//        echo "Debug is " . ($debug ? 'true' : 'false') . "\n";
        $results = null;
        if (!($specification instanceof VisitableSpecification)) {
            echo get_class($specification) . " is not visitable \n";
            return $this->filterResultsBySpecification($this->loadMany($query), $specification);
        }

        list($baseQuery, $extra) = $this->buildMongoQuery($specification);
        if (count($query) > 0) {
            $query['$query'] = $baseQuery;
        } else {
            $query = $baseQuery;
        }
        if ($debug) {
            var_dump($query);
            var_dump($extra);
        }
        $results = $this->loadMany($query);
        if (!$extra) {
            return $results;
        }

        return $this->filterResultsBySpecification($results, $extra);
    }

    protected function applyStore($entity)
    {
        try {
            $id = $entity->getId();
            if ($id && !($id instanceof NullIdentity)) {
                $this->update($entity);
            } else {
                $this->insert($entity);
            }
        } catch (\MongoException $e) {
            print_r($entity->flatten()->asArray());
            throw $e;
        }
    }

    protected function insert(Identifiable $entity)
    {
        $document = $entity->flatten()->asArray();

        if (array_key_exists('id', $document)) {
            unset($document['id']);
        }

        $ident = $this->collection->insert($document);

        $entity->setId(new RealIdentity((string)$ident));
    }

    protected function update($entity)
    {
        $document = $entity->flatten()->asArray();

        if (array_key_exists('id', $document)) {
            unset($document['id']);
        }

        $this->collection->update(
            ['_id' => new MongoId((string)$entity->getId())],
            $document
        );
    }

    /** @return object[] */
    protected function loadMany($query)
    {
        $entities = [];
        $cursor = $this->collection->find($query);
        foreach ($cursor as $document) {
            $entities[] = $this->buildEntity($document);
        }

        return $entities;
    }

    /**
     * @return object
     *
     * @throws NoSuchEntityException
     */
    protected function loadOne(array $query, $error = '')
    {
        $document = $this->collection->findOne($query);

        if ($document === null) {
            throw new NoSuchEntityException($error);
        }

        return $this->buildEntity($document);
    }

    /** @return object */
    abstract protected function buildEntity(array $document);

    /** @return array */
    protected function filterResultsBySpecification(array $results, Specification $specification)
    {
        return array_filter(
            $results,
            function ($entity) use ($specification) {
                return $specification->isSatisfiedBy($entity);
            }
        );
    }

    /** @return array[] */
    protected function buildMongoQuery(VisitableSpecification $specification)
    {
        $visitor = $this->createVisitor();
        $specification->accept($visitor);
        $query = $visitor->getQuery();
        $extra = $visitor->getExtraSpecifications();

        return array($query, $extra);
    }

    /**
     * @return MongoVisitor
     */
    abstract protected function createVisitor();
}
