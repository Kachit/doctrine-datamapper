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
    protected $conditions;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * @var array
     */
    protected $groupBy = [];

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * Filter constructor.
     * @param Collection|null $conditions
     */
    public function __construct(Collection $conditions = null)
    {
        $this->conditions = $conditions ?? new Collection();
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions->toArray();
    }

    /**
     * @param string $field
     * @return Condition[]
     */
    public function getConditionsByField(string $field): array
    {
        return $this->conditions->getByField($field);
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasConditionsByField(string $field): bool
    {
        return $this->conditions->hasByField($field);
    }

    /**
     * @param string $field
     * @return FilterInterface
     */
    public function removeConditionsByField(string $field): FilterInterface
    {
        $this->conditions->removeByField($field);
        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @return bool
     */
    public function hasCondition(string $field, string $operator = self::OPERATOR_IS_EQUAL): bool
    {
        return $this->conditions->hasByFieldAndOperator($field, $operator);
    }

    /**
     * @param string $field
     * @param string $operator
     * @return Condition
     */
    public function getCondition(string $field, string $operator = self::OPERATOR_IS_EQUAL): Condition
    {
        return $this->conditions->getByFieldAndOperator($field, $operator);
    }

    /**
     * @param string $field
     * @param string $operator
     * @return FilterInterface
     */
    public function deleteCondition(string $field, string $operator = self::OPERATOR_IS_EQUAL): FilterInterface
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
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return FilterInterface
     */
    public function setFields(array $fields): FilterInterface
    {
        $this->fields = $fields;
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
     * @param string $value
     * @return FilterInterface
     */
    public function include(string $value): FilterInterface
    {
        $this->includes[] = $value;
        return $this;
    }

    /**
     * @param array $values
     * @return FilterInterface
     */
    public function includes(array $values): FilterInterface
    {
        $this->includes = $values;
        return $this;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isIncluded(string $value): bool
    {
        return in_array($value, $this->includes);
    }

    /**
     * @return array
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    /**
     * @return FilterInterface
     */
    public function clear(): FilterInterface
    {
        $this->conditions->clear();
        $this->orderBy = [];
        $this->groupBy = [];
        $this->includes = [];
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
            && empty($this->includes)
        );
    }
}
