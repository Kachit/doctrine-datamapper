<?php
/**
 * Abstract gateway class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Meta\Table;
use Kachit\Database\Query\Builder;
use Kachit\Database\Query\Filter;

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
        $this->buildQuery($queryBuilder, $filter);
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
        $this->buildQuery($queryBuilder, $filter);
        $result = $queryBuilder
            ->execute()
            ->fetch()
        ;
        return ($result) ? $result : [];
    }

    /**
     * @param mixed $pk
     * @return array
     */
    public function fetchByPk($pk)
    {
        $filter = $this->buildPrimaryKeyFilter($pk);
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
        $this->buildQuery($queryBuilder, $filter, true);
        return $queryBuilder
            ->resetQueryPart('select')
            ->select($column)
            ->execute()
            ->fetchColumn()
        ;
    }

    /**
     * @param Filter|null $filter
     * @param null $column
     * @return string
     */
    public function count(Filter $filter = null, $column = null)
    {
        $fieldCount = ($column) ? $column : $this->getMetaTable()->getPrimaryKey();
        $fieldCount = $this->getTableAlias() . '.' . $fieldCount;
        $count = 'COUNT(' . $fieldCount . ')';
        return $this->fetchColumn($count, $filter);
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->getConnection()
            ->createQueryBuilder()
            ->from($this->getTableName(), $this->getTableAlias())
            ->select($this->getTableFields())
        ;
    }

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        $row = $this->getMetaTable()->filterRow($data);
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
        $this->getBuilder()->build($queryBuilder, $filter);
        $queryBuilder
            ->resetQueryPart('select')
            ->resetQueryPart('orderBy')
            ->update($this->getTableName(), $this->getTableAlias())
        ;
        $data = $this->getMetaTable()->filterRow($data);
        foreach ($data as $column => $value) {
            $queryBuilder->set($column, $this->getConnection()->quote($value));
        }
        return $queryBuilder->execute();
    }

    /**
     * @param array $data
     * @param mixed $pk
     * @return int
     */
    public function updateByPk(array $data, $pk)
    {
        $filter = $this->buildPrimaryKeyFilter($pk);
        return $this->update($data, $filter);
    }

    /**
     * @param Filter $filter
     * @return int
     */
    public function delete(Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->delete($this->getTableName());
        $this->getBuilder()->build($queryBuilder, $filter);
        return $queryBuilder->execute();
    }

    /**
     * @param mixed $pk
     * @return int
     */
    public function deleteByPk($pk)
    {
        $filter = $this->buildPrimaryKeyFilter($pk);
        return $this->delete($filter);
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
     * @return string
     */
    protected function getTableFields()
    {
        return $this->getTableAlias() . '.*';
    }

    /**
     * @param mixed $pk
     * @return Filter
     */
    protected function buildPrimaryKeyFilter($pk)
    {
        $filter = (new Filter())->createCondition($this->getMetaTable()->getPrimaryKey(), $pk);
        return $filter;
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

    /**
     * @param QueryBuilder $queryBuilder
     * @param Filter|null $filter
     * @param bool $isAggregated
     */
    public function buildQuery(QueryBuilder $queryBuilder, Filter $filter = null, $isAggregated = false)
    {
        $this->getBuilder()->build($queryBuilder, $filter, $isAggregated);
    }
}