<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 05.02.2016
 * Time: 1:10
 */
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Meta\Table;
use Kachit\Silex\Database\Query\Filter\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class Gateway implements GatewayInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Table
     */
    private $metaTable;

    /**
     * Gateway constructor
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->metaTable = new Table($this->connection, $this->getTableName());
        $this->metaTable->initialize();
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @param Filter|null $filter
     * @return array
     */
    public function fetchAll(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        if($filter) {
            $this->buildQuery($queryBuilder, $filter);
        }
        return $queryBuilder
            ->execute()
            ->fetchAll()
        ;
    }

    /**
     * @param Filter|null $filter
     * @return array
     */
    public function fetch(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        if($filter) {
            $this->buildQuery($queryBuilder, $filter);
        }
        return $queryBuilder
            ->execute()
            ->fetch()
        ;
    }

    /**
     * @param $pkValue
     * @param string $pkColumn
     * @return array
     */
    public function fetchByPk($pkValue, $pkColumn = null)
    {
        $pkColumn = ($pkColumn) ? $pkColumn : $this->metaTable->getPrimaryKey();
        $result = $this->createQueryBuilder()
            ->where($this->getTableAlias() . ".$pkColumn = :$pkColumn")
            ->setParameter($pkColumn, $pkValue)
            ->execute()
            ->fetch()
        ;
        return ($result) ? $result : [];
    }

    /**
     * @param string $column
     * @param mixed $pkValue
     * @param string $pkColumn
     * @return string
     */
    public function fetchColumn($column, $pkValue, $pkColumn = null)
    {
        $pkColumn = ($pkColumn) ? $pkColumn : $this->metaTable->getPrimaryKey();
        return $this->createQueryBuilder()
            ->resetQueryPart('select')
            ->select($column)
            ->where($this->getTableAlias() . ".$pkColumn = :$pkColumn")
            ->setParameter($pkColumn, $pkValue)
            ->execute()
            ->fetchColumn()
        ;
    }

    /**
     * @param Filter|null $filter
     * @return bool|string
     */
    public function count(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $fieldCount = $this->metaTable->getPrimaryKey();
        if($filter) {
            $this->buildQuery($queryBuilder, $filter, true);
            $fieldCount = ($filter->getFieldCount()) ? $filter->getFieldCount() : $fieldCount;
        }
        $fieldCount = $this->getTableAlias() . '.' . $fieldCount;
        $count = 'COUNT(' . $fieldCount . ')';
        $queryBuilder->resetQueryPart('select')->select($count);
        return $queryBuilder
            ->execute()
            ->fetchColumn()
        ;
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->getConnection()
            ->createQueryBuilder()
            ->from("{$this->getTableName()}", $this->getTableAlias())
            ->select($this->getTableAlias() . '.*')
        ;
    }

    /**
     * @param array $data
     * @return array
     */
    public function createEmptyRow(array $data = [])
    {
        $columns = $this->getTableColumns();
        $row = [];
        foreach ($columns as $column) {
            $row[$column] = (isset($data[$column])) ? $data[$column] : null;
        }
        unset($row[$this->metaTable->getPrimaryKey()]);
        return $row;
    }

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        $row = $this->createEmptyRow($data);
        $result = $this->getConnection()->insert($this->getTableName(), $row);
        return ($result) ? $this->getConnection()->lastInsertId() : $result;
    }

    /**
     * @param array $data
     * @param $pkValue
     * @param null $pkColumn
     * @return int
     */
    public function update(array $data, $pkValue, $pkColumn = null)
    {
        $pkColumn = ($pkColumn) ? $pkColumn : $this->metaTable->getPrimaryKey();
        $row = $this->createEmptyRow($data);
        return $this->getConnection()->update($this->getTableName(), $row, [$pkColumn => $pkValue]);
    }

    /**
     * @param $pkValue
     * @param null $pkColumn
     * @return int
     */
    public function delete($pkValue, $pkColumn = null)
    {
        $pkColumn = ($pkColumn) ? $pkColumn : $this->metaTable->getPrimaryKey();
        return $this->getConnection()->delete($this->getTableName(), [$pkColumn => $pkValue]);
    }

    /**
     * @param Filter $filter = null
     * @return int
     */
    public function deleteAll(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        if($filter) {
            $this->buildQuery($queryBuilder, $filter);
        }
        $queryBuilder
            ->resetQueryPart('select')
            ->resetQueryPart('orderBy')
            ->delete($this->getTableName(), $this->getTableAlias())
        ;
        return $queryBuilder->execute();
    }

    /**
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @return string
     */
    protected function getTableAlias()
    {
        return 't';
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Filter $filter
     * @param bool $isCount
     */
    protected function buildQuery(QueryBuilder $queryBuilder, Filter $filter, $isCount = false)
    {
        $columns = $this->getTableColumns();
        if ($filter->getLimit() && !$isCount) {
            $queryBuilder->setMaxResults($filter->getLimit());
        }
        if ($filter->getOffset() && !$isCount) {
            $queryBuilder->setFirstResult($filter->getOffset());
        }
        $this->buildQueryConditions($queryBuilder, $filter->getConditions(), $columns);

        foreach ($filter->getOrderBy() as $field => $order) {
            if (in_array($field, $columns) && !$isCount) {
                $queryBuilder->addOrderBy($this->getTableAlias() . '.' . $field, $order);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $conditions
     * @param array $columns
     * @param string $tableAlias
     */
    protected function buildQueryConditions(QueryBuilder $queryBuilder, array $conditions, array $columns = [], $tableAlias = null)
    {
        $tableAlias = ($tableAlias) ? $tableAlias : $this->getTableAlias();
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

    /**
     * @return array
     */
    protected function getTableColumns()
    {
        return $this->metaTable->getColumns();
    }
}