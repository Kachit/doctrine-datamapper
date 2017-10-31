<?php
/**
 * Abstract gateway class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Kachit\Database\Query\Builder;
use Kachit\Database\Query\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Kachit\Database\Query\FilterInterface;

abstract class Gateway implements GatewayInterface
{
    /**
     * @var Connection
     */
    private $connection;

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
     * @param int $cacheLifetime
     * @return array
     */
    public function fetchAll(Filter $filter = null, int $cacheLifetime = 0): array
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->buildQuery($queryBuilder, $filter);
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
            $this->getDefaultCacheProfile($cacheLifetime)
        );
        $data = $stmt->fetchAll();
        $stmt->closeCursor();
        return $data;
    }

    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return array
     */
    public function fetch(Filter $filter = null, int $cacheLifetime = 0): array
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->buildQuery($queryBuilder, $filter);
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
            $this->getDefaultCacheProfile($cacheLifetime)
        );
        $result = $stmt->fetch();
        return ($result) ? $result : [];
    }

    /**
     * @param mixed $pk
     * @param string $pkField
     * @param int $cacheLifetime
     * @return array
     */
    public function fetchByPk($pk, string $pkField = self::DEFAULT_PRIMARY_KEY, int $cacheLifetime = 0): array
    {
        $filter = $this->buildPrimaryKeyFilter($pkField, $pk);
        return $this->fetch($filter);
    }

    /**
     * @param string $column
     * @param Filter|null $filter
     * @return mixed
     */
    public function fetchColumn(string $column, Filter $filter = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->buildQuery($queryBuilder, $filter);
        return $queryBuilder
            ->resetQueryPart('select')
            ->resetQueryPart('orderBy')
            ->select($column)
            ->execute()
            ->fetchColumn()
        ;
    }

    /**
     * @param Filter|null $filter
     * @param string|null $column
     * @return int
     */
    public function count(Filter $filter = null, string $column = '*'): int
    {
        $column = $this->getTableAlias() . '.' . $column;
        $count = 'COUNT(' . $column . ')';
        return (int)$this->fetchColumn($count, $filter);
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
     * @return int|bool
     */
    public function insert(array $data)
    {
        $result = $this->getConnection()->insert($this->getTableName(), $data);
        return ($result) ? $this->getConnection()->lastInsertId() : $result;
    }

    /**
     * @param array $data
     * @param Filter $filter
     * @return int
     */
    public function update(array $data, Filter $filter = null): int
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->getBuilder()->build($queryBuilder, $filter);
        $queryBuilder
            ->resetQueryPart('select')
            ->resetQueryPart('orderBy')
            ->update($this->getTableName(), $this->getTableAlias())
        ;
        foreach ($data as $column => $value) {
            $queryBuilder->set($column, $this->getConnection()->quote($value));
        }
        return (int)$queryBuilder->execute();
    }

    /**
     * @param array $data
     * @param mixed $pk
     * @param string $pkField
     * @return int
     */
    public function updateByPk(array $data, $pk, string $pkField = self::DEFAULT_PRIMARY_KEY): int
    {
        $filter = $this->buildPrimaryKeyFilter($pkField, $pk);
        return $this->update($data, $filter);
    }

    /**
     * @param Filter $filter
     * @return int
     */
    public function delete(Filter $filter = null): int
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->delete($this->getTableName());
        $this->getBuilder()->build($queryBuilder, $filter);
        return (int)$queryBuilder->execute();
    }

    /**
     * @param mixed $pk
     * @param string $pkField
     * @return int
     */
    public function deleteByPk($pk, string $pkField = self::DEFAULT_PRIMARY_KEY): int
    {
        $filter = $this->buildPrimaryKeyFilter($pkField, $pk);
        return $this->delete($filter);
    }

    /**
     * @return string
     */
    protected function getTableAlias(): string
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
     * @param string $pkField
     * @param mixed $pk
     * @return FilterInterface
     */
    protected function buildPrimaryKeyFilter(string $pkField, $pk)
    {
        $filter = (new Filter())->createCondition($pkField, $pk);
        return $filter;
    }

    /**
     * @return Builder
     */
    protected function getBuilder()
    {
        if (empty($this->builder)) {
            $this->builder = new Builder($this->getTableAlias());
        }
        return $this->builder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Filter|null $filter
     */
    public function buildQuery(QueryBuilder $queryBuilder, Filter $filter = null)
    {
        $this->getBuilder()->build($queryBuilder, $filter);
    }

    /**
     * @param int $lifetime
     * @param string|null $cacheKey
     * @return QueryCacheProfile
     */
    protected function getDefaultCacheProfile(int $lifetime, string $cacheKey = null)
    {
        return new QueryCacheProfile($lifetime, $cacheKey);
    }
}