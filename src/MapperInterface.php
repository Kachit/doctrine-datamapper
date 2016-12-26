<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 17.12.2016
 * Time: 19:38
 */
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Query\Filter;

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
     * @param EntityInterface $entity
     * @return mixed
     */
    public function save(EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity);
}