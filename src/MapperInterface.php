<?php
/**
 * Mapper interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\CacheInterface;

interface MapperInterface
{
    /**
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache
     * @return EntityInterface[]|CollectionInterface
     */
    public function fetchAll(FilterInterface $filter = null, CacheInterface $cache = null): CollectionInterface;

    /**
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache
     * @return EntityInterface
     */
    public function fetch(FilterInterface $filter = null, CacheInterface $cache = null): EntityInterface;

    /**
     * @param mixed $pk
     * @param CacheInterface $cache
     * @return EntityInterface
     */
    public function fetchByPk($pk, CacheInterface $cache = null): EntityInterface;

    /**
     * @param FilterInterface|null $filter
     * @return int
     */
    public function count(FilterInterface $filter = null): int;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity): bool;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool;

    /**
     * @return GatewayInterface
     */
    public function getTableGateway(): GatewayInterface;
}
