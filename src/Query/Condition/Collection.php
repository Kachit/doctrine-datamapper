<?php
/**
 * Class Collection
 *
 * @author Kachit
 * @package Kachit\Database\Query
 */
namespace Kachit\Database\Query\Condition;

use Kachit\Database\Query\Condition;
use Traversable;

class Collection
{
    /**
     * @var Condition[][]
     */
    private $data = [];

    /**
     * @param Condition $condition
     * @return Collection
     */
    public function add(Condition $condition): Collection
    {
        $this->data[$condition->getField()][$condition->getOperator()] = $condition;
        return $this;
    }

    /**
     * @param string $field
     * @return Condition[]
     */
    public function getByField(string $field): array
    {
        return $this->data[$field] ?? [];
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasByField(string $field): bool
    {
        return isset($this->data[$field]);
    }

    /**
     * @param string $field
     * @return Collection
     */
    public function removeByField(string $field)
    {
        unset($this->data[$field]);
        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @return Condition
     */
    public function getByFieldAndOperator(string $field, string $operator)
    {
        return $this->data[$field][$operator];
    }

    /**
     * @param string $field
     * @param string $operator
     * @return Collection
     */
    public function removeByFieldAndOperator(string $field, string $operator)
    {
        unset($this->data[$field][$operator]);
        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @return bool
     */
    public function hasByFieldAndOperator(string $field, string $operator): bool
    {
        return isset($this->data[$field][$operator]);
    }

    /**
     * @return Collection
     */
    public function clear(): Collection
    {
        $this->data = [];
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
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
}
