<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 05.02.2016
 * Time: 1:10
 */
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Meta\Table;
use Kachit\Silex\Database\Query\Builder;
use Kachit\Silex\Database\Query\Filter;

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
     * @var Builder
     */
    private $builder;

    /**
     * Gateway constructor
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
        $this->getBuilder()->build($queryBuilder, $filter);
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
        $this->getBuilder()->build($queryBuilder, $filter);
        return $queryBuilder
            ->execute()
            ->fetch()
        ;
    }

    /**
     * @param mixed $pk
     * @return array
     */
    public function fetchByPk($pk)
    {
        $pkColumn = $this->metaTable->getPrimaryKey();
        $filter = (new Filter())->createCondition($pkColumn, $pk);
        return $this->fetch($filter);
    }

    /**
     * @param string $column
     * @param Filter|null $filter
     * @return string
     */
    public function fetchColumn($column, Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->getBuilder()->build($queryBuilder, $filter, true);
        return $queryBuilder
            ->resetQueryPart('select')
            ->select($column)
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
        $fieldCount = ($filter->getFieldCount()) ? $filter->getFieldCount() : $this->metaTable->getPrimaryKey();
        $fieldCount = $this->getTableAlias() . '.' . $fieldCount;
        $count = 'COUNT(' . $fieldCount . ')';
        return $this->fetchColumn($count, $filter);
    }

    /**
     * @param Filter|null $filter
     * @return bool|string
     */
    public function sum(Filter $filter = null)
    {
        $fieldSum = ($filter->getFieldSum()) ? $filter->getFieldSum() : $this->metaTable->getPrimaryKey();
        $fieldSum = $this->getTableAlias() . '.' . $fieldSum;
        $sum = 'SUM(' . $fieldSum . ')';
        return $this->fetchColumn($sum, $filter);
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->getConnection()
            ->createQueryBuilder()
            ->from($this->getTableName(), $this->getTableAlias())
            ->select($this->getTableAlias() . '.*')
        ;
    }

    /**
     * @param array $data
     * @return array
     */
    public function createEmptyRow(array $data = [])
    {
        return array_merge($this->getMetaTable()->getDefaultRow(), $data);
    }

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        $row = $this->createEmptyRow($data);
        if (isset($row[$this->getMetaTable()->getPrimaryKey()])) {
            unset($row[$this->getMetaTable()->getPrimaryKey()]);
        }
        $result = $this->getConnection()->insert($this->getTableName(), $row);
        return ($result) ? $this->getConnection()->lastInsertId() : $result;
    }

    /**
     * @param array $data
     * @param Filter $filter
     * @return int
     */
    public function update(array $data, Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->builder->build($queryBuilder, $filter);
        $queryBuilder
            ->resetQueryPart('select')
            ->resetQueryPart('orderBy')
            ->update($this->getTableName(), $this->getTableAlias())
        ;
        foreach ($data as $column => $value)
        {
            $queryBuilder->set($column, $value);
        }
        return $queryBuilder->execute();
    }

    /**
     * @param Filter $filter
     * @return int
     */
    public function delete(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->getBuilder()->build($queryBuilder, $filter);
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
     * @return Table
     */
    protected function getMetaTable()
    {
        if (empty($this->metaTable)) {
            $this->metaTable = new Table($this->getConnection(), $this->getTableName());
            $this->metaTable->initialize();
        }
        return $this->metaTable;
    }

    /**
     * @return Builder
     */
    protected function getBuilder()
    {
        if (empty($this->builder)) {
            $this->builder = new Builder($this->getMetaTable()->getColumns(), $this->getTableAlias());
        }
        return $this->builder;
    }
}