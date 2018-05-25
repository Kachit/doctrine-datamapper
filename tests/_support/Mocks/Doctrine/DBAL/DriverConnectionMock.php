<?php
/**
 * Class DriverConnectionMock
 *
 * @package Kachit\Database\Mocks\Doctrine\DBAL
 * @author Kachit
 */
namespace Kachit\Database\Mocks\Doctrine\DBAL;

use Doctrine\DBAL\Driver\Connection;

class DriverConnectionMock implements Connection
{
    public function prepare($prepareString)
    {
        $statement = new StatementMock();
        return $statement;
    }

    /**
     *
     */
    public function query()
    {
        $statement = new StatementMock();
        return $statement;
    }

    public function quote($input, $type=\PDO::PARAM_STR) {}
    public function exec($statement) {}
    public function lastInsertId($name = null) {}
    public function beginTransaction() {}
    public function commit() {}
    public function rollBack() {}
    public function errorCode() {}
    public function errorInfo() {}
}
