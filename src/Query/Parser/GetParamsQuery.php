<?php
/**
 * Class GetParamsQuery
 *
 * @package Kachit\Database\Query\Parser
 * @author Kachit
 */
namespace Kachit\Database\Query\Parser;

use Kachit\Database\Query\FilterInterface;

class GetParamsQuery extends JsonQuery
{
    const QUERY_PARAM_FILTER = 'filter';
    const QUERY_PARAM_ORDER_BY = 'order';
    const QUERY_PARAM_GROUP_BY = 'group';
    const QUERY_PARAM_LIMIT = 'limit';
    const QUERY_PARAM_OFFSET = 'offset';
    const QUERY_PARAM_INCLUDE = 'include';

    /**
     * @param array $query
     * @return FilterInterface
     */
    public function parse($query): FilterInterface
    {
        $filter = parent::parse($query);
        if (is_array($query)) {
            $this->parseIncludes($filter, $query);
        }
        return $filter;
    }

    /**
     * @param FilterInterface $filter
     * @param array $query
     */
    protected function parseOrderBy(FilterInterface $filter, array $query)
    {
        if (isset($query[static::QUERY_PARAM_ORDER_BY]) && is_array($query[static::QUERY_PARAM_ORDER_BY])) {
            $orders = [self::ORDER_ASC, self::ORDER_DESC];
            foreach ($query[static::QUERY_PARAM_ORDER_BY] as $field => $order) {
                if (in_array(strtolower($order), $orders)) {
                    $filter->addOrderBy($field, $order);
                }
            }
        }
    }

    /**
     * @param FilterInterface $filter
     * @param array $query
     */
    protected function parseIncludes(FilterInterface $filter, array $query)
    {
        if (isset($query[static::QUERY_PARAM_INCLUDE])) {
            $includes = explode(',', $query[static::QUERY_PARAM_INCLUDE]);
            foreach ($includes as $include) {
                $filter->include($include);
            }
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
                    $operator = $this->paramsOperatorsMap[$param];
                    if (in_array($operator, [FilterInterface::OPERATOR_IS_NOT_IN, FilterInterface::OPERATOR_IS_IN])) {
                        $value = explode(',', $value);
                    }
                    $filter->createCondition($field, $value, $operator);
                }
            }
        }
    }
}
