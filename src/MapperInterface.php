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
     * @return EntityInterface[]|CollectionInterface
     */
    public function fetchAll(Filter $filter = null): CollectionInterface;

    /**
     * @param Filter|null $filter
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null): EntityInterface;

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function fetchByPk($pk): EntityInterface;

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
}