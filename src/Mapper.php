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
        $this->hydrator = ($hydrator) ? $hydrator : $this->createDefaultHydrator();
        $this->collection = ($collection) ? $collection : $this->createDefaultCollection();
        $this->metaData = $this->createDefaultMetadata();
        $this->validator = $this->createDefaultValidator();
    }

    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return CollectionInterface|Entity[]
     */
    public function fetchAll(Filter $filter = null, int $cacheLifetime = 0): CollectionInterface
    {
        $data = $this->gateway->fetchAll($filter, $cacheLifetime);
        return $this->hydrateCollection($data);
    }

    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null, int $cacheLifetime = 0): EntityInterface
    {
        $data = $this->gateway->fetch($filter, $cacheLifetime);
        return $this->hydrateEntity($data);
    }

    /**
     * @param mixed $pk
     * @param int $cacheLifetime
     * @return EntityInterface
     */
    public function fetchByPk($pk, int $cacheLifetime = 0): EntityInterface
    {
        $pkField = $this->metaData->getPrimaryKeyColumn();
        $data = $this->gateway->fetchByPk($pk, $pkField, $cacheLifetime);
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
     * @throws MapperException
     */
    public function save(EntityInterface $entity): bool
    {
        $pkField = $this->metaData->getPrimaryKeyColumn();
        $this->validator->validate($entity, $pkField);
        $data = $this->hydrator->extract($entity);
        $data = $this->metaData->filterRow($data);
        $pk = $entity->getEntityField($pkField);
        if ($pk) {
            $result = $this->gateway->updateByPk($data, $pk, $pkField);
        } else {
            $result = $this->gateway->insert($data);
            $pk = $result;
        }
        if ($pk) {
            $entity->setEntityField($pkField, $pk);
            $this->syncEntity($entity);
        }
        return (bool)$result;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     * @throws MapperException
     */
    public function delete(EntityInterface $entity): bool
    {
        $pkField = $this->metaData->getPrimaryKeyColumn();
        $this->validator->validate($entity, $pkField);
        return $this->gateway->deleteByPk($entity->getEntityField($pkField), $pkField);
    }

    /**
     * @return GatewayInterface
     */
    public function getTableGateway(): GatewayInterface
    {
        return $this->gateway;
    }

    /**
     * @param EntityInterface $entity
     */
    protected function syncEntity(EntityInterface $entity)
    {
        $pkField = $this->metaData->getPrimaryKeyColumn();
        $this->validator->validate($entity, $pkField);
        $pk = $entity->getEntityField($pkField);
        $data = $this->gateway->fetchByPk($pk, $pkField);
        $this->hydrator->hydrate($data, $entity);
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
    protected function createDefaultCollection(): Collection
    {
        return new Collection();
    }

    /**
     * @return Hydrator
     */
    protected function createDefaultHydrator(): Hydrator
    {
        return new Hydrator();
    }

    /**
     * @return Validator
     */
    protected function createDefaultValidator(): Validator
    {
        return new Validator($this->entity);
    }

    /**
     * @return MetaDataInterface
     */
    protected function createDefaultMetadata(): MetaDataInterface
    {
        return new Database($this->gateway->getConnection(), $this->gateway->getTableName());
    }
}