<?php
/**
 * Collection interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Countable;
use JsonSerializable;
use IteratorAggregate;
use Kachit\Database\Exception\CollectionException;

interface CollectionInterface extends Countable, JsonSerializable, IteratorAggregate
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
     * @param callable $callback
     * @return CollectionInterface
     */
    public function filter(callable $callback): CollectionInterface;

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

    /**
     * Clear objects
     *
     * @return $this
     */
    public function clear(): CollectionInterface;

    /**
     * Append collection
     *
     * @param Collection $collection
     * @return $this
     */
    public function append(Collection $collection): CollectionInterface;

    /**
     * Apply a user function to every member of an collection
     *
     * @param callable $callback
     * @return CollectionInterface
     */
    public function walk(callable $callback): CollectionInterface;

    /**
     * Map collection items
     *
     * @param callable $callback
     * @return CollectionInterface
     */
    public function map(callable $callback): CollectionInterface;

    /**
     * Sort collection by user function
     *
     * @param callable $callback
     * @return CollectionInterface
     */
    public function sort(callable $callback): CollectionInterface;

    /**
     * Return new collection which has
     *
     * @param int $offset
     * @param int $limit
     * @return CollectionInterface
     */
    public function slice(int $offset, int $limit = null): CollectionInterface;
}
