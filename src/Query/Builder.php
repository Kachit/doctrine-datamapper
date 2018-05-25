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
     * @var string
     */
    private $tableAlias;

    /**
     * Builder constructor.
     *
     * @param $tableAlias
     */
    public function __construct(string $tableAlias)
    {
        $this->tableAlias = $tableAlias;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface|null $filter
     */
    public function build(QueryBuilder $queryBuilder, FilterInterface $filter = null)
    {
        if (empty($filter)) {
            return;
        }
        if ($filter->getLimit()) {
            $queryBuilder->setMaxResults($filter->getLimit());
        }
        if ($filter->getOffset()) {
            $queryBuilder->setFirstResult($filter->getOffset());
        }
        if ($filter->getFields()) {
            $callback = function($element) {
                return $this->tableAlias . '.' . $element;
            };
            $queryBuilder->addSelect(array_map($callback, $filter->getFields()));
        }

        foreach ($filter->getConditions() as $field => $conditions) {
            $this->buildQueryConditions($queryBuilder, $conditions, $this->tableAlias);
        }

        foreach ($filter->getOrderBy() as $field => $order) {
            $queryBuilder->addOrderBy($this->tableAlias . '.' . $field, $order);
        }
        foreach ($filter->getGroupBy() as $field) {
            $queryBuilder->addGroupBy($this->tableAlias . '.' . $field);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $conditions
     * @param string $tableAlias
     */
    public function buildQueryConditions(QueryBuilder $queryBuilder, array $conditions, $tableAlias = null)
    {
        $tableAlias = $tableAlias ? $tableAlias : $this->tableAlias;
        /* @var Condition $condition */
        foreach ($conditions as $condition) {
            $this->buildSingleCondition($queryBuilder, $condition, $tableAlias);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Condition $condition
     * @param $tableAlias
     */
    public function buildSingleCondition(QueryBuilder $queryBuilder, Condition $condition, $tableAlias)
    {
        $expr = $queryBuilder->expr();
        $operator = $condition->getOperator();
        $field = ($queryBuilder->getType() !== QueryBuilder::DELETE && $tableAlias) ? $tableAlias . '.' . $condition->getField() : $condition->getField();
        $type = ($condition->isList()) ? Connection::PARAM_STR_ARRAY : null;
        $value = $condition->getValue();
        if (in_array($operator, [FilterInterface::OPERATOR_IS_NULL])) {
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