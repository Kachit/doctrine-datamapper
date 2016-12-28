<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 17.12.2016
 * Time: 20:00
 */
namespace Kachit\Silex\Database;

interface CollectionInterface
{
    /**
     * @param EntityInterface $entity
     * @return CollectionInterface
     */
    public function add(EntityInterface $entity);

    /**
     * @param mixed $index
     * @return EntityInterface
     */
    public function get($index);

    /**
     * @param mixed $index
     * @return bool
     */
    public function has($index);

    /**
     * @return int
     */
    public function count();
    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return EntityInterface[]
     */
    public function toArray();
}