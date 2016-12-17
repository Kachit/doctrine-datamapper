<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 26.10.2016
 * Time: 20:30
 */
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Query\Filter\Filter;

abstract class Mapper
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
        $this->hydrator = $gateway;
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
     * @param array $data
     * @return EntityInterface
     */
    public function createEntity(array $data = [])
    {
        return $this->hydrator->hydrate($data);
    }

    /**
     * @return Collection
     */
    public function createCollection()
    {
        return clone $this->collection;
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
}