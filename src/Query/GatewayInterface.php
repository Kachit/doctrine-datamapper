<?php
namespace Kachit\Silex\Database;

use Kachit\Silex\Database\Query\Filter\Filter;

interface GatewayInterface
{
    /**
     * @param Filter|null $filter
     * @return array
     */
    public function fetchAll(Filter $filter = null);

    /**
     * @param Filter|null $filter
     * @return array
     */
    public function fetch(Filter $filter = null);

    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data);

    /**
     * @param array $data
     * @param $pkValue
     * @param null $pkColumn
     * @return int
     */
    public function update(array $data, $pkValue, $pkColumn = null);

    /**
     * @param $pkValue
     * @param null $pkColumn
     * @return int
     */
    public function delete($pkValue, $pkColumn = null);
}