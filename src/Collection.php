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

class Collection implements CollectionInterface, \JsonSerializable, \IteratorAggregate
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
     * @throws CollectionException
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
        $data = $this->toArray();
        if (empty($data)) {
            throw new CollectionException('Collection is empty');
        }
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
        $data = $this->toArray();
        if (empty($data)) {
            throw new CollectionException('Collection is empty');
        }
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
     * @param Closure $function
     * @return CollectionInterface|EntityInterface[]
     */
    public function filter(Closure $function): CollectionInterface
    {
        $data = array_filter($this->data, $function);
        return new static($data);
    }

    /**
     * Apply a user function to every member of an collection
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
    public function slice($offset, $limit = null): CollectionInterface
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
     */
    public function cloneObject($index): EntityInterface
    {
        return clone $this->get($index);
    }

    /**
     * Clone collection
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
        return new \ArrayIterator($this->data);
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