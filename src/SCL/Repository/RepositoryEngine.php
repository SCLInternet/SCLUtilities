<?php

namespace SCL\Repository;

use MongoId;
use SCL\Repository\Exception\NoSuchEntityException;
use SCL\Repository\Specification\Specification;
use SCL\Repository\Specification\VisitableSpecification;
use SCL\Repository\Value\Identifiable;
use SCL\Repository\Value\Identity;

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
    public function loadBySpecification(Specification $specification)
    {
        return $this->loadBySpecificationWithQuery($specification, []);
    }

    /**
     * @return object
     *
     * @throw NoSuchEntityException
     */
    public function loadOneBySpecification(Specification $specification)
    {
        // @todo maybe should be a version that doesn't throw
        $entities = $this->loadBySpecification($specification);

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
    protected function loadBySpecificationWithQuery(Specification $specification, array $query)
    {
        $results = null;
        if (!($specification instanceof VisitableSpecification)) {
            return $this->filterResultsBySpecification($this->loadMany($query), $specification);
        }

        list($baseQuery, $extra) = $this->buildMongoQuery($specification);
        if (count($query)) {
            $query['query'] = $baseQuery;
        } else {
            $query = $baseQuery;
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
            if ($entity->getId()) {
                $this->update($entity);
            } else {
                $this->insert($entity);
            }
        } catch (\MongoException $e) {
            print_r($entity->flatten()->asArray());
            throw $e;
        }
    }

    protected function insert($entity)
    {
        $document = $entity->flatten()->asArray();

        if (array_key_exists('id', $document)) {
            unset($document['id']);
        }

        $ident = $this->collection->insert($document);

        $entity->setId(new Identity($ident));
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
        foreach ($this->collection->find($query) as $document) {
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
        $visitor = new BMMongoVisitor();
        $specification->accept($visitor);
        $query = $visitor->getQuery();
        $extra = $visitor->getExtraSpecifications();

        return array($query, $extra);
    }
}
