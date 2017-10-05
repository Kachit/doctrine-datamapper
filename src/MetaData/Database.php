<?php
/**
 * Meta table class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\MetaData;

use Doctrine\DBAL\Connection;
use Kachit\Database\MetaDataInterface;

class Database implements MetaDataInterface
{
    protected $fields;

    /**
     * @var mixed
     */
    private $primaryKey;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var string
     */
    private $table;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $metaFieldNames = [
        'pdo_pgsql' => [
            'fieldName' => 'field',
            'defaultValueName' => 'default',
        ],
        'pdo_mysql' => [
            'fieldName' => 'Field',
            'defaultValueName' => 'Default',
        ],
    ];

    /**
     * MetaData constructor.
     * @param Connection $connection
     * @param $table
     */
    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return array_keys($this->columns);
    }

    /**
     * @return array
     */
    public function getDefaultRow()
    {
        return $this->columns;
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterRow(array $data)
    {
        $columns = $this->getColumns();
        foreach ($data as $key => $value) {
            if (!in_array($key, $columns) || is_null($value)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     *
     */
    public function initialize()
    {
        $sql = $this->connection->getDatabasePlatform()->getListTableColumnsSQL($this->table);
        $columns = $this->connection->query($sql)->fetchAll();
        $this->extractMeta($columns);
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @param array $columns
     * @return array
     */
    private function extractMeta(array $columns)
    {
        $result = [];
        foreach ($columns as $column) {
            $columnName = $this->metaFieldNames[$this->connection->getDriver()->getName()]['fieldName'];
            $this->columns[$column[$columnName]] = $this->extractDefaultValue($column);
            $this->extractPrimaryKey($column);
        }
        return $result;
    }

    /**
     * @param array $column
     * @return mixed
     */
    private function extractDefaultValue(array $column)
    {
        $name = $this->metaFieldNames[$this->connection->getDriver()->getName()]['defaultValueName'];
        return $column[$name];
    }

    /**
     * @param array $column
     */
    private function extractPrimaryKey(array $column)
    {
        if (empty($this->primaryKey)) {
            $driver = $this->connection->getDriver()->getName();
            if ($driver == 'pdo_mysql' &&  isset($column['Key']) && $column['Key'] == 'PRI') {
                $columnName = $this->metaFieldNames[$driver]['fieldName'];
                $this->primaryKey = $column[$columnName];
            }
            if ($driver == 'pdo_pgsql' &&  isset($column['pri']) && $column['pri'] == 't') {
                $columnName = $this->metaFieldNames[$driver]['fieldName'];
                $this->primaryKey = $column[$columnName];
            }
        }
    }
}