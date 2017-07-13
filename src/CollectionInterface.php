<?php
/**
 * Collection interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Closure;
use Kachit\Database\Exception\CollectionException;

interface CollectionInterface
{
    /**
     * @param EntityInterface $entity
     * @return CollectionInterface
     */
    public function add(EntityInterface $entity): CollectionInterface;

    /**
     * @param mixed $index
     * @throws CollectionException
     * @return EntityInterface
     */
    public function get($index): EntityInterface;

    /**
     * @param mixed $index
     * @return bool
     */
    public function has($index): bool;

    /**
     * @param mixed $index
     * @throws CollectionException
     * @return CollectionInterface
     */
    public function remove($index): CollectionInterface;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param Closure $function
     * @return CollectionInterface
     */
    public function filter(Closure $function): CollectionInterface;

    /**
     * Get first object
     *
     * @throws CollectionException
     * @return EntityInterface
     */
    public function getFirst(): EntityInterface;

    /**
     * Get last object
     *
     * @throws CollectionException
     * @return EntityInterface
     */
    public function getLast(): EntityInterface;

    /**
     * @return array
     */
    public function getKeys(): array;

    /**
     * @param string $valueField
     * @param string|null $keyField
     * @return array
     */
    public function extract(string $valueField, string $keyField = null): array;

    /**
     * @return EntityInterface[]
     */
    public function toArray(): array;
}