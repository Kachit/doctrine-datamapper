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

class Builder implements BuilderInterface
{
    /**
     * @var string
     */
    protected $tableAlias;

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
                return $this->addTableAliasToField($element, $this->tableAlias);
            };
            $queryBuilder->addSelect(array_map($callback, $filter->getFields()));
        }

        foreach ($filter->getConditions() as $field => $conditions) {
            $this->buildQueryConditions($queryBuilder, $conditions, $this->tableAlias);
        }

        foreach ($filter->getOrderBy() as $field => $order) {
            $queryBuilder->addOrderBy($this->addTableAliasToField($field, $this->tableAlias), $order);
        }
        foreach ($filter->getGroupBy() as $field) {
            $queryBuilder->addGroupBy($this->addTableAliasToField($field, $this->tableAlias));
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Condition[] $conditions
     * @param string $tableAlias
     */
    public function buildQueryConditions(QueryBuilder $queryBuilder, array $conditions, $tableAlias = null)
    {
        $tableAlias = $tableAlias ? $tableAlias : $this->tableAlias;
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
        $needNamedParameter = true;

        $expr = $queryBuilder->expr();
        $operator = $condition->getOperator();
        $field = $condition->getField();
        $field = ($queryBuilder->getType() !== QueryBuilder::DELETE) ? $this->addTableAliasToField($field, $tableAlias) : $field;
        $type = ($condition->isList()) ? Connection::PARAM_STR_ARRAY : null;
        $value = $condition->getValue();

        if ($operator == FilterInterface::OPERATOR_IS_NULL) {
            $operator = $queryBuilder->getConnection()->getDatabasePlatform()->getIsNullExpression('');
            $needNamedParameter = false;
        }

        if ($operator == FilterInterface::OPERATOR_IS_NOT_NULL) {
            $operator = $queryBuilder->getConnection()->getDatabasePlatform()->getIsNotNullExpression('');
            $needNamedParameter = false;
        }

        if (is_bool($value)) {
            $type = \PDO::PARAM_BOOL;
        }

        if ($needNamedParameter) {
            $namedParameter = $queryBuilder->createNamedParameter($value, $type);

            if($condition->isList()) {
                $namedParameter = '(' . $namedParameter . ')';
            }
            $where = $expr->comparison($field, $operator, $namedParameter);
        } else {
            $where = $expr->comparison($field, $operator, '');
        }

        $queryBuilder
            ->andWhere($where)
        ;
    }

    /**
     * @param string $field
     * @param string $tableAlias
     * @return string
     */
    protected function addTableAliasToField(string $field, $tableAlias): string
    {
        return ($tableAlias && !strpos($field, '.')) ? $tableAlias . '.' . $field : $field;
    }
}
