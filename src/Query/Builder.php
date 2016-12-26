<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 02.08.2016
 * Time: 11:26
 */
namespace Kachit\Silex\Database\Query;

use Doctrine\DBAL\Query\QueryBuilder;

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
     */
    public function build(QueryBuilder $queryBuilder, Filter $filter = null)
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
        $this->buildQueryConditions($queryBuilder, $filter->getConditions(), $this->columns);

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
        $tableAlias = ($tableAlias) ? $tableAlias : $this->tableAlias;
        foreach ($conditions as $condition) {
            if ($columns && !in_array($condition->getField(), $columns)) {
                continue;
            }
            $queryBuilder
                ->andWhere($tableAlias . '.' . $condition->getConditionString())
                ->setParameter($condition->getNamedParam(), $condition->getValue(), $condition->getType())
            ;
        }
    }
}