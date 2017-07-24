<?php
/**
 * Class GetParamsQuery
 *
 * @package Kachit\Database\Query\Parser
 * @author Kachit
 */
namespace Kachit\Database\Query\Parser;

use Kachit\Database\Query\Filter;
use Kachit\Database\Query\FilterInterface;

class GetParamsQuery extends JsonQuery
{
    const QUERY_PARAM_FILTER = 'filter';
    const QUERY_PARAM_ORDER_BY = 'order';
    const QUERY_PARAM_GROUP_BY = 'group';
    const QUERY_PARAM_LIMIT = 'limit';
    const QUERY_PARAM_OFFSET = 'offset';

    /**
     * @param array $query
     * @return FilterInterface
     */
    public function parse($query): FilterInterface
    {
        return parent::parse($query);
    }

    /**
     * @param Filter $filter
     * @param $field
     * @param $conditions
     */
    protected function parseConditions(Filter $filter, $field, $conditions)
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
