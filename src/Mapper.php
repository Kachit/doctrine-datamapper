<?php
/**
 * Abstract mapper class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Query\Filter;

class Mapper implements MapperInterface
{
    /**
     * @var GatewayInterface
     */
    protected $gateway;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * Mapper constructor
     *
     * @param GatewayInterface $gateway
     * @param EntityInterface $entity
     * @param HydratorInterface|null $hydrator
     * @param CollectionInterface|null $collection
     */
    public function __construct(GatewayInterface $gateway, EntityInterface $entity, HydratorInterface $hydrator = null, CollectionInterface $collection = null)
    {
        $this->gateway = $gateway;
        $this->entity = $entity;
        $this->hydrator = ($hydrator) ? $hydrator : new Hydrator();
        $this->collection = ($collection) ? $collection : new Collection();
    }

    /**
     * @param Filter|null $filter
     * @return CollectionInterface|Entity[]
     */
    public function fetchAll(Filter $filter = null)
    {
        $collection = $this->cloneCollection();
        $data = $this->gateway->fetchAll($filter);
        foreach ($data as $row) {
            $entity = $this->hydrator->hydrate($row, $this->cloneEntity());
            $collection->add($entity);
        }
        return $collection;
    }

    /**
     * @param Filter|null $filter
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null)
    {
        $data = $this->gateway->fetch($filter);
        return $this->hydrator->hydrate($data, $this->cloneEntity());
    }

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function fetchByPk($pk)
    {
        $data = $this->gateway->fetchByPk($pk);
        return $this->hydrator->hydrate($data, $this->cloneEntity());
    }

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function count(Filter $filter = null)
    {
        return $this->gateway->count($filter);
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function save(EntityInterface $entity)
    {
        $this->validateEntity($entity);
        $pk = $entity->getPk();
        if ($pk) {
            $this->gateway->updateByPk($entity->toArray(), $pk);
        } else {
            $pk = $this->gateway->insert($entity->toArray());
        }
        return $this->fetchByPk($pk);
    }

    /**
     * @param EntityInterface $entity
     * @return int
     */
    public function delete(EntityInterface $entity)
    {
        $this->validateEntity($entity);
        return $this->gateway->deleteByPk($entity->getPk());
    }



    /**
     * @param GatewayInterface $gateway
     * @return $this
     */
    public function setGateway(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * @param CollectionInterface $collection
     * @return $this
     */
    public function setCollection(CollectionInterface $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @param EntityInterface $entity
     * @throws \Exception
     */
    protected function validateEntity(EntityInterface $entity)
    {
        $entityClass = get_class($this->entity);
        if ($entity->isNull()) {
            throw new \Exception(sprintf('Entity "%s" is null', get_class($entity)));
        }
        if (!$entity instanceof $entityClass) {
            throw new \Exception(sprintf('Entity "%s" is not valid', get_class($entity)));
        }
    }

    /**
     * @return CollectionInterface
     */
    protected function cloneCollection()
    {
        return clone $this->collection;
    }

    /**
     * @return EntityInterface
     */
    protected function cloneEntity()
    {
        return clone $this->entity;
    }
}