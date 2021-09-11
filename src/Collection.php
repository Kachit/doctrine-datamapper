<?php
/**
 * Collection class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Exception\CollectionException;
use Traversable;
use Closure;
use ArrayIterator;

class Collection implements CollectionInterface
{
    /**
     * @var EntityInterface[]
     */
    protected $data = [];

    /**
     * Collection constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * @param EntityInterface $entity
     * @return CollectionInterface
     */
    public function add(EntityInterface $entity): CollectionInterface
    {
        $this->data[] = $entity;
        return $this;
    }

    /**
     * @param EntityInterface[] $entities
     * @return CollectionInterface
     */
    public function fill(array $entities): CollectionInterface
    {
        foreach($entities as $entity) {
            $this->add($entity);
        }
        return $this;
    }

    /**
     * @param mixed $index
     * @return EntityInterface
     * @throws CollectionException
     */
    public function get($index): EntityInterface
    {
        if (!$this->has($index)) {
            throw new CollectionException(sprintf('Entity with index "%s" is not exists', $index));
        }
        return $this->data[$index];
    }

    /**
     * @param mixed $index
     * @return CollectionInterface
     * @throws CollectionException
     */
    public function remove($index): CollectionInterface
    {
        if (!$this->has($index)) {
            throw new CollectionException(sprintf('Entity with index "%s" is not exists', $index));
        }
        unset($this->data[$index]);
        return $this;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * @param mixed $index
     * @return bool
     */
    public function has($index): bool
    {
        return isset($this->data[$index]);
    }

    /**
     * Get first object
     *
     * @throws CollectionException
     * @return EntityInterface
     */
    public function getFirst(): EntityInterface
    {
        if ($this->isEmpty()) {
            throw new CollectionException('Collection is empty');
        }
        $data = $this->toArray();
        return array_shift($data);
    }
    /**
     * Get last object
     *
     * @throws CollectionException
     * @return EntityInterface
     */
    public function getLast(): EntityInterface
    {
        if ($this->isEmpty()) {
            throw new CollectionException('Collection is empty');
        }
        $data = $this->toArray();
        return array_pop($data);
    }

    /**
     * @param string $valueField
     * @param string|null $keyField
     * @return array
     */
    public function extract(string $valueField, string $keyField = null): array
    {
        $result = [];
        /* @var EntityInterface $entity */
        foreach ($this as $entity) {
            $value = $entity->getEntityField($valueField);
            if ($keyField) {
                $key = $entity->getEntityField($keyField);
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * @return EntityInterface[]
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Filter collection by user function
     *
     * @param callable $callback
     * @return CollectionInterface|EntityInterface[]
     */
    public function filter(callable $callback): CollectionInterface
    {
        $data = array_filter($this->data, $callback);
        return new static($data);
    }

    /**
     * Apply a user function to every item of an collection
     *
     * @param callable $callback
     * @return CollectionInterface
     */
    public function walk(callable $callback): CollectionInterface
    {
        array_walk($this->data, $callback);
        return $this;
    }

    /**
     * Map collection items
     *
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->data);
    }

    /**
     * Sort collection by user function
     *
     * @param callable $callback
     * @return CollectionInterface
     */
    public function sort(callable $callback): CollectionInterface
    {
        uasort($this->data, $callback);
        return $this;
    }

    /**
     * Return new collection which has
     *
     * @param int $offset
     * @param int $limit
     * @return CollectionInterface
     */
    public function slice(int $offset, int $limit = null): CollectionInterface
    {
        $data = array_slice($this->data, $offset, $limit, true);
        return new static($data);
    }

    /**
     * Get object keys
     *
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Clear objects
     *
     * @return CollectionInterface
     */
    public function clear(): CollectionInterface
    {
        $this->data = [];
        return $this;
    }

    /**
     * Append collection
     *
     * @param Collection $collection
     * @return CollectionInterface
     */
    public function append(Collection $collection): CollectionInterface
    {
        foreach ($collection as $object) {
            $this->add($object);
        }
        return $this;
    }

    /**
     * Get cloned object
     *
     * @param mixed $index
     * @return EntityInterface
     * @throws CollectionException
     */
    public function cloneObject($index): EntityInterface
    {
        return clone $this->get($index);
    }

    /**
     * Clone collection
     *
     * @throws CollectionException
     */
    public function __clone()
    {
        $data = [];
        foreach ($this->getKeys() as $index) {
            $data[$index] = $this->cloneObject($index);
        }
        $this->data = $data;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
