<?php
namespace Kachit\Silex\Database\Query\Filter;

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
     * @return Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return array
     */
    public function getConditionsByFields()
    {
        $data = [];
        foreach ($this->conditions as $condition) {
            $data[$condition->getField()][] = $condition;
        }
        return $data;
    }

    /**
     * @param Condition[] $conditions
     * @return $this
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        $this->conditions[] = $condition;
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
        $this->conditions[] = $condition;
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
        $this->limit = 0;
        $this->offset = 0;
        $this->fieldAvg = null;
        $this->fieldSum = null;
        $this->fieldCount = null;
        return $this;
    }
}