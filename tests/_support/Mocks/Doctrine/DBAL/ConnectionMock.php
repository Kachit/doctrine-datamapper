<?php
/**
 * Class ConnectionMock
 *
 * @package Kachit\Database\Mocks\Doctrine\DBAL
 * @author Kachit
 */
namespace Kachit\Database\Mocks\Doctrine\DBAL;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Cache\ArrayStatement;
use Doctrine\DBAL\DBALException;

class ConnectionMock extends Connection
{
    /**
     * @var DatabasePlatformMock
     */
    private $platformMock;

    /**
     * @var int
     */
    private $lastInsertId = 1;

    /**
     * @var array
     */
    private $inserts = [];

    /**
     * @var array
     */
    private $updates = [];

    /**
     * @var array
     */
    private $fetchResults = [];

    /**
     * ConnectionMock constructor.
     * @param array $params
     * @param \Doctrine\DBAL\Driver $driver
     * @param null $config
     * @param null $eventManager
     */
    public function __construct(array $params, $driver, $config = null, $eventManager = null)
    {
        $this->platformMock = new DatabasePlatformMock();
        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * @return array
     */
    public function getLastQuery(): array
    {
        $queries = $this->queries;
        return ($queries) ? array_pop($queries) : [];
    }

    /**
     * @return bool
     */
    public function connect()
    {
        return true;
    }

    public function query()
    {
        $data = ($this->fetchResults) ? array_shift($this->fetchResults) : [];
        $statement = new ArrayStatement($data);
        return $statement;
    }

    /**
     * Executes an, optionally parametrized, SQL query.
     *
     * If the query is parametrized, a prepared statement is used.
     * If an SQLLogger is configured, the execution is logged.
     *
     * @param string                                      $query  The SQL query to execute.
     * @param array                                       $params The parameters to bind to the query, if any.
     * @param array                                       $types  The types the previous parameters are in.
     * @param \Doctrine\DBAL\Cache\QueryCacheProfile|null $qcp    The query cache profile, optional.
     *
     * @return \Doctrine\DBAL\Driver\ResultStatement The executed statement.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        $this->queries[] = [
            'query' => $query,
            'params' => $params
        ];
        $data = ($this->fetchResults) ? array_shift($this->fetchResults) : [];
        $statement = new ArrayStatement($data);
        return $statement;
    }

    /**
     * @override
     */
    public function getDatabasePlatform()
    {
        return $this->platformMock;
    }

    /**
     * Inserts a table row with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string         $table The expression of the table to insert data into, quoted or unquoted.
     * @param mixed[]        $data  An associative array containing column-value pairs.
     * @param int[]|string[] $types Types of the inserted data.
     *
     * @return int The number of affected rows.
     *
     * @throws DBALException
     */
    public function insert($tableName, array $data, array $types = array())
    {
        $this->inserts[$tableName][] = $data;
        $this->lastInsertId++;
        return $this->lastInsertId;
    }

    /**
     * Executes an SQL UPDATE statement on a table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression  The expression of the table to update quoted or unquoted.
     * @param array  $data       An associative array containing column-value pairs.
     * @param array  $identifier The update criteria. An associative array containing column-value pairs.
     * @param array  $types      Types of the merged $data and $identifier arrays in that order.
     *
     * @return integer The number of affected rows.
     */
    public function update($tableExpression, array $data, array $identifier, array $types = array())
    {
        $this->updates[$tableExpression][] = $data;
        return 1;
    }

    /**
     * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters
     * and returns the number of affected rows.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string $query  The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The parameter types.
     *
     * @return integer The number of affected rows.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        $this->updates[$query] = $params;
        return 1;
    }

    /**
     * @param null $seqName
     * @return int|string
     */
    public function lastInsertId($seqName = null)
    {
        return $this->lastInsertId;
    }

    /**
     * @override
     */
    public function fetchColumn($statement, array $params = array(), $colnum = 0, array $types = array())
    {
        return 1;
    }

    /**
     * @override
     */
    public function quote($input, $type = null)
    {
        if (is_string($input)) {
            return "'" . $input . "'";
        }
        return $input;
    }

    /**
     * @param array $result
     * @return $this
     */
    public function addFetchResult(array $result)
    {
        $this->fetchResults[] = $result;
        return $this;
    }

    /**
     * @return array
     */
    public function getInserts(): array
    {
        return $this->inserts;
    }

    /**
     * @return array
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }


    /**
     * @return array
     */
    public function getFetchResults(): array
    {
        return $this->fetchResults;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->inserts = [];
        $this->updates = [];
        $this->queries = [];
        $this->fetchResults = [];
        $this->lastInsertId = 0;
        return $this;
    }
}
