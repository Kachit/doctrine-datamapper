<?php
/**
 * Query builder class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;

class Builder
{
    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var string
     */
    private $tableAlias;

    /**
     * Builder constructor.
     *
     * @param array $columns
     * @param $tableAlias
     */
    public function __construct(array $columns, $tableAlias)
    {
        $this->columns = $columns;
        $this->tableAlias = $tableAlias;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Filter|null $filter
     * @param bool $isAggregated
     */
    public function build(QueryBuilder $queryBuilder, Filter $filter = null, $isAggregated = false)
    {
        if (empty($filter)) {
            return;
        }
        if ($filter->getLimit() && !$isAggregated) {
            $queryBuilder->setMaxResults($filter->getLimit());
        }
        if ($filter->getOffset() && !$isAggregated) {
            $queryBuilder->setFirstResult($filter->getOffset());
        }

        foreach ($filter->getConditions() as $field => $conditions) {
            $this->buildQueryConditions($queryBuilder, $conditions, $this->columns, $this->tableAlias);
        }

        foreach ($filter->getOrderBy() as $field => $order) {
            if (in_array($field, $this->columns)) {
                $queryBuilder->addOrderBy($this->tableAlias . '.' . $field, $order);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $conditions
     * @param array $columns
     * @param string $tableAlias
     */
    public function buildQueryConditions(QueryBuilder $queryBuilder, array $conditions, array $columns = [], $tableAlias = null)
    {
        /* @var Condition $condition */
        foreach ($conditions as $condition) {
            if ($columns && !in_array($condition->getField(), $columns)) {
                continue;
            }
            $this->buildExpression($queryBuilder, $condition, $tableAlias);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Condition $condition
     * @param $tableAlias
     */
    protected function buildExpression(QueryBuilder $queryBuilder, Condition $condition, $tableAlias)
    {
        $expr = $queryBuilder->expr();
        $operator = $condition->getOperator();
        $field = ($queryBuilder->getType() !== QueryBuilder::DELETE) ? $tableAlias . '.' . $condition->getField() : $condition->getField();
        $type = ($condition->isList()) ? Connection::PARAM_STR_ARRAY : null;
        $value = $condition->getValue();
        if (in_array($operator, [Parser::OPERATOR_IS_NULL])) {
            $value = ($value) ? 'IS NOT NULL' : 'IS NULL';
        }
        $namedParameter = $queryBuilder->createNamedParameter($value, $type);

        if($condition->isList()) {
            $namedParameter = '(' . $namedParameter . ')';
        }
        $where = $expr->comparison($field, $operator, $namedParameter);
        $queryBuilder
            ->andWhere($where)
        ;
    }
}