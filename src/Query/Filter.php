<?php
/**
 * Query filter class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\Query;

use Kachit\Database\Query\Condition\Collection;

class Filter implements FilterInterface
{
    /**
     * @var Collection
     */
    private $conditions;

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * Filter constructor.
     * @param Collection|null $conditions
     */
    public function __construct(Collection $conditions = null)
    {
        $this->conditions = $conditions ?? new Collection();
    }

    /**
     * @param string $field
     * @param string $operator
     * @return bool
     */
    public function hasCondition(string $field, string $operator): bool
    {
        return $this->conditions->hasByFieldAndOperator($field, $operator);
    }

    /**
     * @param string $field
     * @param string $operator
     * @return Condition
     */
    public function getCondition(string $field, string $operator): Condition
    {
        return $this->conditions->getByFieldAndOperator($field, $operator);
    }

    /**
     * @param string $field
     * @param string $operator
     * @return FilterInterface
     */
    public function deleteCondition(string $field, string $operator): FilterInterface
    {
        $this->conditions->removeByFieldAndOperator($field, $operator);
        return $this;
    }

    /**
     * @param Condition $condition
     * @return FilterInterface
     */
    public function addCondition(Condition $condition): FilterInterface
    {
        $this->conditions->add($condition);
        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return FilterInterface
     */
    public function createCondition(string $field, $value, string $operator = self::OPERATOR_IS_EQUAL): FilterInterface
    {
        $this->addCondition(new Condition($field, $operator, $value));
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return FilterInterface
     */
    public function setLimit(int $limit): FilterInterface
    {
        $this->limit = (int)$limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return FilterInterface
     */
    public function setOffset(int $offset): FilterInterface
    {
        $this->offset = (int)$offset;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param array $orderBy
     * @return FilterInterface
     */
    public function setOrderBy(array $orderBy): FilterInterface
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param string $field
     * @param string $order
     * @return FilterInterface
     */
    public function addOrderBy(string $field, string $order): FilterInterface
    {
        $this->orderBy[$field] = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @param array $groupBy
     * @return FilterInterface
     */
    public function setGroupBy(array $groupBy): FilterInterface
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param string $field
     * @return FilterInterface
     */
    public function addGroupBy(string $field): FilterInterface
    {
        $this->groupBy[] = $field;
        return $this;
    }

    /**
     * @return FilterInterface
     */
    public function clear(): FilterInterface
    {
        $this->conditions->clear();
        $this->orderBy = [];
        $this->groupBy = [];
        $this->limit = 0;
        $this->offset = 0;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return ($this->conditions->isEmpty()
            && empty($this->orderBy)
            && empty($this->groupBy)
            && empty($this->limit)
            && empty($this->offset)
        );
    }
}