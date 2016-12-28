<?php
namespace Kachit\Silex\Database\Query;

class Filter
{
    /**
     * @var Condition[]
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
     * @var null
     */
    private $fieldCount = null;

    /**
     * @var null
     */
    private $fieldSum = null;

    /**
     * @var null
     */
    private $fieldAvg = null;

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
        $this->conditions[$condition->getField()][] = $condition;
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
     * @return null
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     * @param null $fieldCount
     * @return $this
     */
    public function setFieldCount($fieldCount)
    {
        $this->fieldCount = $fieldCount;
        return $this;
    }

    /**
     * @return null
     */
    public function getFieldSum()
    {
        return $this->fieldSum;
    }

    /**
     * @param null $fieldSum
     * @return $this
     */
    public function setFieldSum($fieldSum)
    {
        $this->fieldSum = $fieldSum;
        return $this;
    }

    /**
     * @return null
     */
    public function getFieldAvg()
    {
        return $this->fieldAvg;
    }

    /**
     * @param null $fieldAvg
     * @return $this
     */
    public function setFieldAvg($fieldAvg)
    {
        $this->fieldAvg = $fieldAvg;
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
        $this->fieldAvg = null;
        $this->fieldSum = null;
        $this->fieldCount = null;
        return $this;
    }
}