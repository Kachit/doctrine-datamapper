<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 26.10.2016
 * Time: 20:30
 */
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Query\Filter\Filter;

class Mapper implements MapperInterface
{
    /**
     * @var GatewayInterface
     */
    private $gateway;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CollectionInterface
     */
    private $collection;

    /**
     * Mapper constructor
     *
     * @param GatewayInterface $gateway
     * @param HydratorInterface $hydrator
     * @param CollectionInterface $collection
     */
    public function __construct(GatewayInterface $gateway, HydratorInterface $hydrator, CollectionInterface $collection = null)
    {
        $this->gateway = $gateway;
        $this->hydrator = $hydrator;
        $this->collection = ($collection) ? $collection : new Collection();
    }

    /**
     * @param Filter|null $filter
     * @return Collection|Entity[]
     */
    public function fetchAll(Filter $filter = null)
    {
        $collection = $this->createCollection();
        $data = $this->gateway->fetchAll($filter);
        foreach ($data as $row) {
            $collection->add($this->createEntity($row));
        }
        return $collection;
    }

    /**
     * @param EntityInterface $entity
     * @return int
     */
    public function save(EntityInterface $entity)
    {
        if ($entity->isNull()) {

        }
        $data = $this->hydrator->extract($entity);
        return $this->gateway->insert($data);
    }

    /**
     * @param EntityInterface $entity
     */
    public function delete(EntityInterface $entity)
    {
        if ($entity->isNull()) {

        }
    }

    /**
     * @param Filter|null $filter
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null)
    {
        $data = $this->gateway->fetch($filter);
        return $this->createEntity($data);
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
     * @return EntityInterface
     */
    protected function createEntity(array $data = [])
    {
        return $this->hydrator->hydrate($data);
    }

    /**
     * @return Collection
     */
    protected function createCollection()
    {
        return clone $this->collection;
    }
}