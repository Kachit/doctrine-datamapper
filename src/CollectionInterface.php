<?php
/**
 * Collection interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

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