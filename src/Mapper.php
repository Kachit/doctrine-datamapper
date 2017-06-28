<?php
/**
 * Abstract mapper class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Exception\MapperException;
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
    public function fetchAll(Filter $filter = null): CollectionInterface
    {
        $data = $this->gateway->fetchAll($filter);
        return $this->hydrateCollection($data);
    }

    /**
     * @param Filter|null $filter
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null): EntityInterface
    {
        $data = $this->gateway->fetch($filter);
        return $this->hydrateEntity($data);
    }

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function fetchByPk($pk): EntityInterface
    {
        $data = $this->gateway->fetchByPk($pk);
        return $this->hydrateEntity($data);
    }

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function count(Filter $filter = null): int
    {
        return $this->gateway->count($filter);
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity)
    {
        $this->validateEntity($entity);
        $pk = $entity->getPk();
        $data = $this->hydrator->extract($entity);
        if ($pk) {
            $result = $this->gateway->updateByPk($data, $pk);
        } else {
            $result = $this->gateway->insert($data);
            $entity->setPk($result);
        }
        return $result;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool
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
     * @param array $data
     * @return EntityInterface|NullEntity
     */
    protected function hydrateEntity(array $data): EntityInterface
    {
        return $this->hydrator->hydrate($data, $this->createEntity());
    }

    /**
     * @param array $data
     * @return CollectionInterface
     */
    protected function hydrateCollection(array $data): CollectionInterface
    {
        $collection = $this->createCollection();
        foreach ($data as $row) {
            $entity = $this->hydrateEntity($row);
            $collection->add($entity);
        }
        return $collection;
    }

    /**
     * @param EntityInterface $entity
     * @throws MapperException
     */
    protected function validateEntity(EntityInterface $entity)
    {
        $expectedClass = get_class($this->entity);
        $actualClass = get_class($entity);
        if ($entity->isNull()) {
            throw new MapperException(sprintf('Entity "%s" is null', $actualClass));
        }
        if (!$entity instanceof $expectedClass) {
            throw new MapperException(sprintf('Entity "%s" is not valid', $actualClass));
        }
    }

    /**
     * @return CollectionInterface
     */
    protected function createCollection()
    {
        return clone $this->collection;
    }

    /**
     * @return EntityInterface
     */
    protected function createEntity()
    {
        return clone $this->entity;
    }
}