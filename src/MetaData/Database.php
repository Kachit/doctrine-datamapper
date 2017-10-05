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
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Column;

class Database implements MetaDataInterface
{
    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $columns;

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
     * @param string $tableName
     */
    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->table = new Table($tableName);
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $this->initialize();
        return array_keys($this->columns);
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterRow(array $data): array
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
     * @return void
     */
    public function initialize()
    {
        if (empty($this->initialized)) {
            $sql = $this->connection->getDatabasePlatform()->getListTableColumnsSQL($this->table->getName());
            $columns = $this->connection->query($sql)->fetchAll();
            $this->initialized = true;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKeyColumn(): string
    {
        $this->initialize();
        return 'id';
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