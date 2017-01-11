<?php
/**
 * Query filter class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\Query;

class Filter
{
    /**
     * @var array
     */
    private $conditions = [];

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
     * @param null $field
     * @return array
     */
    public function getConditions($field = null)
    {
        return ($field) ? $this->conditions[$field] : $this->conditions;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasConditions($field)
    {
        return isset($this->conditions[$field]);
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        $this->conditions[$condition->getField()][$condition->getOperator()] = $condition;
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function createCondition($field, $value, $operator = '=')
    {
        $condition = new Condition($field, $operator, $value);
        $this->addCondition($condition);
        return $this;
    }

    /**
     * @param string $field
     * @return Filter
     */
    public function deleteConditions($field)
    {
        if ($this->hasConditions($field)) {
            unset($this->conditions[$field]);
        }
        return $this;
    }
    /**
     * @param string $field
     * @param string $operator
     * @return bool
     */
    public function hasCondition($field, $operator)
    {
        return isset($this->conditions[$field][$operator]);
    }
    /**
     * @param string $field
     * @param string $operator
     * @return Condition|null
     */
    public function getCondition($field, $operator)
    {
        return $this->hasCondition($field, $operator) ? $this->conditions[$field][$operator] : null;
    }

    /**
     * @param string $field
     * @param string $operator
     * @return Filter
     */
    public function deleteCondition($field, $operator)
    {
        if ($this->hasCondition($field, $operator)) {
            unset($this->conditions[$field][$operator]);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = (int)$offset;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param array $orderBy
     * @return $this
     */
    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param $field
     * @param $order
     * @return $this
     */
    public function addOrderBy($field, $order)
    {
        $this->orderBy[$field] = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @param array $groupBy
     * @return $this
     */
    public function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function addGroupBy($field)
    {
        $this->groupBy[] = $field;
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->conditions = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->limit = 0;
        $this->offset = 0;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (empty($this->conditions)
            && empty($this->orderBy)
            && empty($this->groupBy)
            && empty($this->limit)
            && empty($this->offset)
        );
    }
}