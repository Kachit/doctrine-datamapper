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
     * @return EntityInterface[]|Collection
     */
    public function fetchAll(Filter $filter = null);

    /**
     * @param Filter|null $filter
     * @return EntityInterface
     */
    public function fetch(Filter $filter = null);

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function fetchByPk($pk);

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function count(Filter $filter = null);

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function save(EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity);
}