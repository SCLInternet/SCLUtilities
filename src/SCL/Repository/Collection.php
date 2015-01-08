<?php

namespace SCL\Repository;

use MongoCollection;
use MongoCursor;
use MongoId;

class Collection
{
    /** @var MongoCollection */
    private $collection;

    public function __construct(MongoCollection $collection)
    {
        $this->collection = $collection;
    }

    /** @return bool */
    public function hasAny(array $query = [])
    {
        return $this->count($query) > 0;
    }

    /** @return int */
    public function count(array $query = [])
    {
        return $this->collection->find($query)->count();
    }

    /** @return MongoCursor */
    public function find(array $query = [], array $fields = [])
    {
        return $this->collection->find($query, $fields);
    }

    /** @return array */
    public function findOne(array $query = [], array $fields = [])
    {
        return $this->collection->findOne($query, $fields);
    }

    /**
     * @param array|object $a
     *
     * @return MongoId
     */
    public function insert($a, array $options = [])
    {
        /** @todo Throw on error */
        try {
            $this->collection->insert($a, $options);

            return $a['_id'];
        } catch (\MongoException $e) {
            var_dump($a);
            throw $e;
        }
    }

    public function update(array $criteria, array $new, array $options = [])
    {
        return $this->collection->update($criteria, $new, $options);
    }

    /** @return bool|array */
    public function remove(array $criteria = [], array $options = [])
    {
        return $this->collection->remove($criteria, $options);
    }
}
