<?php
/**
 * Query parser class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\Query;

class Parser
{
    const QUERY_PARAM_FILTER = '$filter';
    const QUERY_PARAM_ORDER_BY = '$orderby';
    const QUERY_PARAM_GROUP_BY = '$group';
    const QUERY_PARAM_LIMIT = '$limit';
    const QUERY_PARAM_OFFSET = '$skip';

    const OPERATOR_IS_EQUAL = '=';
    const OPERATOR_IS_NOT_EQUAL = '!=';
    const OPERATOR_IS_GREATER_THAN = '>';
    const OPERATOR_IS_GREATER_THAN_OR_EQUAL = '>=';
    const OPERATOR_IS_IN = 'IN';
    const OPERATOR_IS_NOT_IN = 'NOT IN';
    const OPERATOR_IS_LESS_THAN = '<';
    const OPERATOR_IS_LESS_THAN_OR_EQUAL = '<=';
    const OPERATOR_IS_LIKE = 'LIKE';
    const OPERATOR_IS_NULL = 'NULL';

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
        self::PARAM_IS_EQUAL => self::OPERATOR_IS_EQUAL,
        self::PARAM_IS_NOT_EQUAL => self::OPERATOR_IS_NOT_EQUAL,
        self::PARAM_IS_GREATER_THAN => self::OPERATOR_IS_GREATER_THAN,
        self::PARAM_IS_GREATER_THAN_OR_EQUAL => self::OPERATOR_IS_GREATER_THAN_OR_EQUAL,
        self::PARAM_IS_IN => self::OPERATOR_IS_IN,
        self::PARAM_IS_NOT_IN => self::OPERATOR_IS_NOT_IN,
        self::PARAM_IS_LESS_THAN => self::OPERATOR_IS_LESS_THAN,
        self::PARAM_IS_LESS_THAN_OR_EQUAL => self::OPERATOR_IS_LESS_THAN_OR_EQUAL,
        self::PARAM_IS_LIKE => self::OPERATOR_IS_LIKE,
        self::PARAM_IS_NULL => self::OPERATOR_IS_NULL,
    ];

    /**
     * @param string $query
     * @return Filter
     */
    public function parse($query)
    {
        $filter = new Filter();
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
     * @param Filter $filter
     * @param array $query
     */
    protected function parseFilter(Filter $filter, array $query)
    {
        if (!isset($query[self::QUERY_PARAM_FILTER]) || !is_array($query[self::QUERY_PARAM_FILTER])) {
            return;
        }
        foreach ($query[self::QUERY_PARAM_FILTER] as $field => $conditions) {
            $this->parseConditions($filter, $field, $conditions);
        }
    }

    /**
     * @param Filter $filter
     * @param $field
     * @param $conditions
     */
    protected function parseConditions(Filter $filter, $field, $conditions)
    {
        if (is_scalar($conditions)) {
            $filter->addCondition(new Condition($field, self::OPERATOR_IS_EQUAL, $conditions));
        } else {
            foreach ($conditions as $param => $value) {
                if (isset($this->paramsOperatorsMap[$param])) {
                    $filter->addCondition(new Condition($field, $this->paramsOperatorsMap[$param], $value));
                }
            }
        }
    }

    /**
     * @param Filter $filter
     * @param array $query
     */
    protected function parseLimitOffset(Filter $filter, array $query)
    {
        if (isset($query[self::QUERY_PARAM_LIMIT])) {
            $filter->setLimit($this->filterValue($query[self::QUERY_PARAM_LIMIT], FILTER_SANITIZE_NUMBER_INT ));
        }
        if (isset($query[self::QUERY_PARAM_OFFSET])) {
            $filter->setOffset($this->filterValue($query[self::QUERY_PARAM_OFFSET], FILTER_SANITIZE_NUMBER_INT ));
        }
    }

    /**
     * @param Filter $filter
     * @param array $query
     */
    protected function parseOrderBy(Filter $filter, array $query)
    {
        if (isset($query[self::QUERY_PARAM_ORDER_BY]) && is_array($query[self::QUERY_PARAM_ORDER_BY])) {
            $orders = [self::ORDER_ASC, self::ORDER_DESC];
            foreach ($query[self::QUERY_PARAM_ORDER_BY] as $orderBy) {
                foreach ($orderBy as $field => $order) {
                    if (in_array(strtolower($order), $orders)) {
                        $filter->addOrderBy($field, $order);
                    }
                }
            }
        }
    }

    /**
     * @param Filter $filter
     * @param array $query
     */
    protected function parseGroupBy(Filter $filter, array $query)
    {
        if (isset($query[self::QUERY_PARAM_GROUP_BY]) && is_array($query[self::QUERY_PARAM_GROUP_BY])) {
            foreach ($query[self::QUERY_PARAM_GROUP_BY] as $groupBy) {
                foreach ($groupBy as $field) {
                    $filter->addGroupBy($field);
                }
            }
        }
    }

    /**
     * @param $value
     * @param int $filter
     * @param null $options
     * @return mixed
     */
    protected function filterValue($value, $filter = FILTER_SANITIZE_STRING, $options = null)
    {
        return filter_var($value, $filter, $options);
    }
}