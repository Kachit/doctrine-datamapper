<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 05.02.2016
 * Time: 1:10
 */
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Query\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class Gateway implements GatewayInterface
{
    const PRIMARY_KEY_FIELD = 'id';
    const TABLE_ALIAS = 't';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $tableColumns = [];

    /**
     * Gateway constructor
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->setConnection($connection);
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
        $pkColumn = ($pkColumn) ? $pkColumn : static::PRIMARY_KEY_FIELD;
        $result = $this->createQueryBuilder()
            ->where(static::TABLE_ALIAS . ".$pkColumn = :$pkColumn")
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
        $pkColumn = ($pkColumn) ? $pkColumn : static::PRIMARY_KEY_FIELD;
        return $this->createQueryBuilder()
            ->resetQueryPart('select')
            ->select($column)
            ->where(static::TABLE_ALIAS . ".$pkColumn = :$pkColumn")
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
        $fieldCount = static::PRIMARY_KEY_FIELD;
        if($filter) {
            $this->buildQuery($queryBuilder, $filter, true);
            $fieldCount = ($filter->getFieldCount()) ? $filter->getFieldCount() : $fieldCount;
        }
        $fieldCount = static::TABLE_ALIAS . '.' . $fieldCount;
        $count = 'COUNT(' . $fieldCount . ')';
        $queryBuilder->resetQueryPart('select')->select($count);
        return $queryBuilder
            ->execute()
            ->fetchColumn()
        ;
    }

    /**
     * @param Filter|null $filter
     * @return bool|string
     */
    public function sum(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $fieldSum = static::PRIMARY_KEY_FIELD;
        if($filter) {
            $this->buildQuery($queryBuilder, $filter, true);
            $fieldSum = ($filter->getFieldSum()) ? $filter->getFieldSum() : $fieldSum;
        }
        $fieldSum = static::TABLE_ALIAS . '.' . $fieldSum;
        $sum = 'SUM(' . $fieldSum . ')';
        $queryBuilder->resetQueryPart('select')->select($sum);
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
            ->from("{$this->getTableName()}", static::TABLE_ALIAS)
            ->select(static::TABLE_ALIAS . '.*')
        ;
    }

    /**
     * @return array
     */
    public function getTableColumns()
    {
        if (empty($this->tableColumns)) {
            $columns = $this->getConnection()->query("SHOW COLUMNS FROM {$this->getTableName()}")->fetchAll();
            $this->tableColumns = (is_array($columns)) ? array_column($columns, 'Field') : [];
        }
        return $this->tableColumns;
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
        unset($row[static::PRIMARY_KEY_FIELD]);
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
        $pkColumn = ($pkColumn) ? $pkColumn : static::PRIMARY_KEY_FIELD;
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
        $pkColumn = ($pkColumn) ? $pkColumn : static::PRIMARY_KEY_FIELD;
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
            ->delete($this->getTableName(), static::TABLE_ALIAS)
        ;
        return $queryBuilder->execute();
    }

    /**
     * @return string
     */
    abstract public function getTableName();

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
                $queryBuilder->addOrderBy(static::TABLE_ALIAS . '.' . $field, $order);
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
        $tableAlias = ($tableAlias) ? $tableAlias : static::TABLE_ALIAS;
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