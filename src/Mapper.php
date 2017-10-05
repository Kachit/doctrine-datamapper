<?php
/**
 * Abstract mapper class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Exception\MapperException;
use Kachit\Database\MetaData\Database;
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
     * @var EntityValidatorInterface
     */
    protected $validator;

    /**
     * @var MetaDataInterface
     */
    protected $metaData;

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
        $this->hydrator = ($hydrator) ? $hydrator : $this->getDefaultHydrator();
        $this->collection = ($collection) ? $collection : $this->getDefaultCollection();
        $this->metaData = $this->getDefaultMetadata();
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
    public function save(EntityInterface $entity): bool
    {
        $pk = $entity->getPk();
        $data = $this->hydrator->extract($entity);
        if ($pk) {
            $result = $this->gateway->updateByPk($data, $pk);
        } else {
            $result = $this->gateway->insert($data);
            $entity->setPk($result);
        }
        return (bool)$result;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool
    {
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

    /**
     * @return Collection
     */
    protected function getDefaultCollection(): Collection
    {
        return new Collection();
    }

    /**
     * @return Hydrator
     */
    protected function getDefaultHydrator(): Hydrator
    {
        return new Hydrator();
    }

    /**
     * @return MetaDataInterface
     */
    protected function getDefaultMetadata(): MetaDataInterface
    {
        return new Database($this->gateway->getConnection(), 'qwerty');
    }
}