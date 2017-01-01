<?php
namespace Kachit\Database;

use Kachit\Database\Query\Filter;

interface GatewayInterface
{
    /**
     * @param Filter|null $filter
     * @return array
     */
    public function fetchAll(Filter $filter = null);

    /**
     * @param Filter|null $filter
     * @return mixed
     */
    public function fetch(Filter $filter = null);

    /**
     * @param mixed $pk
     * @return array|mixed
     */
    public function fetchByPk($pk);

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function count(Filter $filter = null);

    /**
     * @param string $column
     * @param Filter|null $filter
     * @return mixed
     */
    public function fetchColumn($column, Filter $filter = null);

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data);

    /**
     * @param array $data
     * @param mixed $pk
     * @return int
     */
    public function updateByPk(array $data, $pk);

    /**
     * @param array $data
     * @param Filter|null $filter
     * @return int
     */
    public function update(array $data, Filter $filter = null);

    /**
     * @param mixed $pk
     * @return int
     */
    public function deleteByPk($pk);

    /**
     * @param Filter|null $filter
     * @return int
     */
    public function delete(Filter $filter = null);
}