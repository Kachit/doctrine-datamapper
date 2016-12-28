<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 02.08.2016
 * Time: 11:26
 */
namespace Kachit\Silex\Database\Query;

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
        $namedParam = $namedParamComparison = $queryBuilder->createNamedParameter($condition->getField());
        $expr = $queryBuilder->expr();
        $operator = $condition->getOperator();
        $field = $tableAlias . '.' . $condition->getField();
        $type = null;
        $value = $condition->getValue();
        if (in_array($operator, [Parser::OPERATOR_IS_LIKE])) {
            $value = '%' . $value . '%';
        }
        if (in_array($operator, [Parser::OPERATOR_IS_IN, Parser::OPERATOR_IS_NOT_IN])) {
            $type = Connection::PARAM_STR_ARRAY;
            $namedParamComparison = '(' . $namedParamComparison . ')';
        }
        if (in_array($operator, [Parser::OPERATOR_IS_NULL])) {
            $value = ($value) ? 'IS NOT NULL' : 'IS NULL';
        }
        $queryBuilder
            ->andWhere($expr->comparison($field, $operator, $namedParamComparison))
            ->setParameter($namedParam, $value, $type)
        ;
    }
}