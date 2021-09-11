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
use Kachit\Database\Query\BuilderInterface;
use Kachit\Database\Query\Cache;
use Kachit\Database\Query\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\CacheInterface;
use Kachit\Database\Gateway\Configuration;

abstract class Gateway implements GatewayInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * Gateway constructor
     *
     * @param Connection $connection
     * @param Configuration $configuration
     */
    public function __construct(Connection $connection, Configuration $configuration = null)
    {
        $this->connection = $connection;
        $this->configuration = ($configuration) ?? new Configuration();
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
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
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache
     * @return array
     */
    public function fetchAll(FilterInterface $filter = null, CacheInterface $cache = null): array
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->buildQuery($queryBuilder, $filter);
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
            $this->getDefaultCacheProfile($cache)
        );
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }

    /**
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache
     * @return array
     */
    public function fetch(FilterInterface $filter = null, CacheInterface $cache = null): array
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->buildQuery($queryBuilder, $filter);
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
            $this->getDefaultCacheProfile($cache)
        );
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return ($result) ? $result[0] : [];
    }

    /**
     * @param mixed $pk
     * @param string $pkField
     * @param CacheInterface $cache
     * @return array
     */
    public function fetchByPk($pk, string $pkField = MetaDataInterface::DEFAULT_PRIMARY_KEY, CacheInterface $cache = null): array
    {
        $filter = $this->buildPrimaryKeyFilter($pkField, $pk);
        return $this->fetch($filter, $cache);
    }

    /**
     * @param string $column
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache = null
     * @return mixed
     */
    public function fetchColumn(string $column, FilterInterface $filter = null, CacheInterface $cache = null)
    {
        $queryBuilder = $this->createQueryBuilder();
        $queryBuilder->select($column);
        $this->buildQuery($queryBuilder, $filter);
        $stmt = $this->connection->executeQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
            $this->getDefaultCacheProfile($cache)
        );
        $result = $stmt->fetchColumn();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param FilterInterface|null $filter
     * @param string|null $column
     * @return int
     */
    public function count(FilterInterface $filter = null, string $column = '*'): int
    {
        $column = $this->getTableAlias() . '.' . $column;
        $count = 'COUNT(' . $column . ')';
        return (int)$this->fetchColumn($count, $filter);
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder(): QueryBuilder
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
     * @param FilterInterface $filter
     * @return int
     */
    public function update(array $data, FilterInterface $filter = null): int
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
    public function updateByPk(array $data, $pk, string $pkField = MetaDataInterface::DEFAULT_PRIMARY_KEY): int
    {
        $filter = $this->buildPrimaryKeyFilter($pkField, $pk);
        return $this->update($data, $filter);
    }

    /**
     * @param FilterInterface $filter
     * @return int
     */
    public function delete(FilterInterface $filter = null): int
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
    public function deleteByPk($pk, string $pkField = MetaDataInterface::DEFAULT_PRIMARY_KEY): int
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
    protected function buildPrimaryKeyFilter(string $pkField, $pk): FilterInterface
    {
        return (new Filter())->createCondition($pkField, $pk);
    }

    /**
     * @return BuilderInterface
     */
    protected function getBuilder(): BuilderInterface
    {
        if (empty($this->builder)) {
            $this->builder = new Builder($this->getTableAlias());
        }
        return $this->builder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface|null $filter
     */
    public function buildQuery(QueryBuilder $queryBuilder, FilterInterface $filter = null)
    {
        $this->getBuilder()->build($queryBuilder, $filter);
    }

    /**
     * @param CacheInterface $cache
     * @return QueryCacheProfile|CacheInterface|null
     */
    protected function getDefaultCacheProfile(CacheInterface $cache = null)
    {
        if (empty($cache)) {
            $configuration = $this->connection->getConfiguration();
            $cacheAdapter = $configuration->getResultCacheImpl();
            if ($cacheAdapter && $this->configuration->getCacheLifeTime()) {
                $cache = new Cache($this->configuration->getCacheLifeTime(), $this->configuration->getCacheKey(), $cacheAdapter);
            }
        }
        return $cache;
    }
}
