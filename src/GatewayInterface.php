<?php
/**
 * Gateway interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\CacheInterface;

interface GatewayInterface
{
    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache
     * @return array
     */
    public function fetchAll(FilterInterface $filter = null, CacheInterface $cache = null): array;

    /**
     * @param FilterInterface|null $filter
     * @param CacheInterface $cache
     * @return array
     */
    public function fetch(FilterInterface $filter = null, CacheInterface $cache = null): array;

    /**
     * @param mixed $pk
     * @param string $pkField
     * @param CacheInterface $cache
     * @return array
     */
    public function fetchByPk($pk, string $pkField = MetaDataInterface::DEFAULT_PRIMARY_KEY, CacheInterface $cache = null): array;

    /**
     * @param FilterInterface|null $filter
     * @param string|null $column
     * @return integer
     */
    public function count(FilterInterface $filter = null, string $column = '*'): int;

    /**
     * @param string $column
     * @param FilterInterface|null $filter
     * @return mixed
     */
    public function fetchColumn(string $column, FilterInterface $filter = null);

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data);

    /**
     * @param array $data
     * @param mixed $pk
     * @param string $pkField
     * @return int
     */
    public function updateByPk(array $data, $pk, string $pkField = MetaDataInterface::DEFAULT_PRIMARY_KEY): int;

    /**
     * @param array $data
     * @param FilterInterface|null $filter
     * @return int
     */
    public function update(array $data, FilterInterface $filter = null): int;

    /**
     * @param mixed $pk
     * @param string $pkField
     * @return int
     */
    public function deleteByPk($pk, string $pkField = MetaDataInterface::DEFAULT_PRIMARY_KEY): int;

    /**
     * @param FilterInterface|null $filter
     * @return int
     */
    public function delete(FilterInterface $filter = null): int;
}