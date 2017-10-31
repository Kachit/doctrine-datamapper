<?php
/**
 * Gateway interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Query\Filter;

interface GatewayInterface
{
    const DEFAULT_PRIMARY_KEY = 'id';

    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return array
     */
    public function fetchAll(Filter $filter = null, int $cacheLifetime = 0): array;

    /**
     * @param Filter|null $filter
     * @param int $cacheLifetime
     * @return array
     */
    public function fetch(Filter $filter = null, int $cacheLifetime = 0): array;

    /**
     * @param mixed $pk
     * @param string $pkField
     * @param int $cacheLifetime
     * @return array
     */
    public function fetchByPk($pk, string $pkField = self::DEFAULT_PRIMARY_KEY, int $cacheLifetime = 0): array;

    /**
     * @param Filter|null $filter
     * @param string|null $column
     * @return integer
     */
    public function count(Filter $filter = null, string $column = '*'): int;

    /**
     * @param string $column
     * @param Filter|null $filter
     * @return mixed
     */
    public function fetchColumn(string $column, Filter $filter = null);

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
    public function updateByPk(array $data, $pk, string $pkField = self::DEFAULT_PRIMARY_KEY): int;

    /**
     * @param array $data
     * @param Filter|null $filter
     * @return int
     */
    public function update(array $data, Filter $filter = null): int;

    /**
     * @param mixed $pk
     * @param string $pkField
     * @return int
     */
    public function deleteByPk($pk, string $pkField = self::DEFAULT_PRIMARY_KEY): int;

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function delete(Filter $filter = null): int;
}