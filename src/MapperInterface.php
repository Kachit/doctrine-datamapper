<?php
/**
 * Mapper interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Query\Filter;

interface MapperInterface
{
    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return EntityInterface[]|CollectionInterface
     */
    public function fetchAll(Filter $filter = null, int $cacheLifetime = 0): CollectionInterface;

    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null, int $cacheLifetime = 0): EntityInterface;

    /**
     * @param mixed $pk
     * @param int $cacheLifetime
     * @return EntityInterface
     */
    public function fetchByPk($pk, int $cacheLifetime = 0): EntityInterface;

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function count(Filter $filter = null): int;

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