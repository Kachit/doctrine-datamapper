<?php
/**
 * Filter builder
 *
 * @package Kachit\Database\Query\Condition
 * @author Kachit
 */
namespace Kachit\Database\Query\Filter;

use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\Filter;

class Builder
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * Builder constructor
     *
     * @param FilterInterface|null $filter
     */
    public function __construct(FilterInterface $filter = null)
    {
        $this->create($filter);
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function eq(string $field, $value)
    {
        $this->filter->createCondition($field, $value);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function neq(string $field, $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_NOT_EQUAL);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function in(string $field, array $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_IN);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function nin(string $field, array $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_NOT_IN);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function gt(string $field, $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_GREATER_THAN);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function gte(string $field, $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_GREATER_THAN_OR_EQUAL);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function lt(string $field, $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_LESS_THAN);
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function lte(string $field, $value)
    {
        $this->filter->createCondition($field, $value, Filter::OPERATOR_IS_LESS_THAN_OR_EQUAL);
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function withNull(string $field)
    {
        $this->filter->createCondition($field, null, Filter::OPERATOR_IS_NULL);
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function withNotNull(string $field)
    {
        $this->filter->createCondition($field, null, Filter::OPERATOR_IS_NOT_NULL);
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->filter->setLimit($limit);
        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->filter->setOffset($offset);
        return $this;
    }

    /**
     * @param string $field
     * @param bool $asc
     * @return $this
     */
    public function order(string $field, bool $asc = true)
    {
        $order = ($asc) ? FilterInterface::ORDER_ASC : Filter::ORDER_DESC;
        $this->filter->addOrderBy($field, $order);
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function group(string $field)
    {
        $this->filter->addGroupBy($field);
        return $this;
    }

    /**
     * @return FilterInterface
     */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    /**
     * @param FilterInterface|null $filter
     * @return $this
     */
    public function create(FilterInterface $filter = null)
    {
        $this->filter = $filter ?? new Filter();
        return $this;
    }
}