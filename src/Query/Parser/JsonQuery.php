<?php
/**
 * Query parser class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\Query\Parser;

use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\ParserInterface;
use Kachit\Database\Query\Filter;

class JsonQuery implements ParserInterface
{
    const QUERY_PARAM_FILTER = '$filter';
    const QUERY_PARAM_ORDER_BY = '$orderby';
    const QUERY_PARAM_GROUP_BY = '$group';
    const QUERY_PARAM_LIMIT = '$limit';
    const QUERY_PARAM_OFFSET = '$skip';

    const PARAM_IS_EQUAL = '$eq';
    const PARAM_IS_GREATER_THAN = '$gt';
    const PARAM_IS_GREATER_THAN_OR_EQUAL = '$gte';
    const PARAM_IS_IN = '$in';
    const PARAM_IS_NOT_IN = '$nin';
    const PARAM_IS_LESS_THAN = '$lt';
    const PARAM_IS_LESS_THAN_OR_EQUAL = '$lte';
    const PARAM_IS_LIKE = '$search';
    const PARAM_IS_NOT_EQUAL = '$ne';
    const PARAM_IS_NULL = '$exists';

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @var array
     */
    protected $paramsOperatorsMap = [
        self::PARAM_IS_EQUAL => FilterInterface::OPERATOR_IS_EQUAL,
        self::PARAM_IS_NOT_EQUAL => FilterInterface::OPERATOR_IS_NOT_EQUAL,
        self::PARAM_IS_GREATER_THAN => FilterInterface::OPERATOR_IS_GREATER_THAN,
        self::PARAM_IS_GREATER_THAN_OR_EQUAL => FilterInterface::OPERATOR_IS_GREATER_THAN_OR_EQUAL,
        self::PARAM_IS_IN => FilterInterface::OPERATOR_IS_IN,
        self::PARAM_IS_NOT_IN => FilterInterface::OPERATOR_IS_NOT_IN,
        self::PARAM_IS_LESS_THAN => FilterInterface::OPERATOR_IS_LESS_THAN,
        self::PARAM_IS_LESS_THAN_OR_EQUAL => FilterInterface::OPERATOR_IS_LESS_THAN_OR_EQUAL,
        self::PARAM_IS_LIKE => FilterInterface::OPERATOR_IS_LIKE,
        self::PARAM_IS_NULL => FilterInterface::OPERATOR_IS_NULL,
    ];

    /**
     * @param mixed $query
     * @param FilterInterface $filter = null
     * @return FilterInterface
     */
    public function parse($query, FilterInterface $filter = null): FilterInterface
    {
        $filter = $filter ?? new Filter();
        if (is_string($query)) {
            $query = json_decode($query, true);
        }
        if (is_array($query)) {
            $this->parseLimitOffset($filter, $query);
            $this->parseFilter($filter, $query);
            $this->parseOrderBy($filter, $query);
            $this->parseGroupBy($filter, $query);
        }
        return $filter;
    }

    /**
     * @param FilterInterface $filter
     * @param array $query
     */
    protected function parseFilter(FilterInterface $filter, array $query)
    {
        if (!isset($query[static::QUERY_PARAM_FILTER]) || !is_array($query[static::QUERY_PARAM_FILTER])) {
            return;
        }
        foreach ($query[static::QUERY_PARAM_FILTER] as $field => $conditions) {
            $this->parseConditions($filter, $field, $conditions);
        }
    }

    /**
     * @param FilterInterface $filter
     * @param $field
     * @param $conditions
     */
    protected function parseConditions(FilterInterface $filter, $field, $conditions)
    {
        if (is_scalar($conditions)) {
            $filter->createCondition($field, $conditions, FilterInterface::OPERATOR_IS_EQUAL);
        } else {
            foreach ($conditions as $param => $value) {
                if (isset($this->paramsOperatorsMap[$param])) {
                    $filter->createCondition($field, $this->filterValue($value), $this->paramsOperatorsMap[$param]);
                }
            }
        }
    }

    /**
     * @param FilterInterface $filter
     * @param array $query
     */
    protected function parseLimitOffset(FilterInterface $filter, array $query)
    {
        if (isset($query[static::QUERY_PARAM_LIMIT])) {
            $filter->setLimit($this->filterValue($query[static::QUERY_PARAM_LIMIT], FILTER_SANITIZE_NUMBER_INT ));
        }
        if (isset($query[static::QUERY_PARAM_OFFSET])) {
            $filter->setOffset($this->filterValue($query[static::QUERY_PARAM_OFFSET], FILTER_SANITIZE_NUMBER_INT ));
        }
    }

    /**
     * @param FilterInterface $filter
     * @param array $query
     */
    protected function parseOrderBy(FilterInterface $filter, array $query)
    {
        if (isset($query[static::QUERY_PARAM_ORDER_BY]) && is_array($query[static::QUERY_PARAM_ORDER_BY])) {
            $orders = [self::ORDER_ASC, self::ORDER_DESC];
            foreach ($query[static::QUERY_PARAM_ORDER_BY] as $orderBy) {
                foreach ($orderBy as $field => $order) {
                    if (in_array(strtolower($order), $orders)) {
                        $filter->addOrderBy($field, $order);
                    }
                }
            }
        }
    }

    /**
     * @param FilterInterface $filter
     * @param array $query
     */
    protected function parseGroupBy(FilterInterface $filter, array $query)
    {
        if (isset($query[static::QUERY_PARAM_GROUP_BY]) && is_array($query[static::QUERY_PARAM_GROUP_BY])) {
            foreach ($query[static::QUERY_PARAM_GROUP_BY] as $groupBy) {
                $filter->addGroupBy($groupBy);
            }
        }
    }

    /**
     * @param mixed $value
     * @param int $filter
     * @param null $options
     * @return mixed
     */
    protected function filterValue($value, $filter = FILTER_SANITIZE_STRING, $options = null)
    {
        return is_array($value) ? $value : filter_var($value, $filter, $options);
    }
}
