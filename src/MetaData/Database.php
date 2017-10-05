<?php
/**
 * Metadata Database class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\MetaData;

use Doctrine\DBAL\Connection;

class Database extends AbstractMetadata
{
    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * MetaData constructor.
     * @param Connection $connection
     * @param string $tableName
     */
    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        $this->initialize();
        return parent::getColumns();
    }

    /**
     * @return void
     */
    protected function initialize()
    {
        if (empty($this->initialized)) {
            $sql = $this->connection->getDatabasePlatform()->getListTableColumnsSQL($this->tableName);
            $columns = $this->connection->query($sql)->fetchAll();
            $this->extractColumnsData($columns);
            $this->initialized = true;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKeyColumn(): string
    {
        $this->initialize();
        return parent::getPrimaryKeyColumn();
    }

    /**
     * @param array $columns
     */
    private function extractColumnsData(array $columns)
    {
        $driverName = $this->connection->getDriver()->getName();
        $columnsMap = $this->getDriversColumnsMap()[$driverName];
        foreach ($columns as $column) {
            $columnName = $columnsMap['columnNameField'];
            $this->columns[] = $column[$columnName];
            if (empty($this->primaryKey)) {
                if (isset($column[$columnsMap['columnNamePrimaryKeyField']]) &&
                    $column[$columnsMap['columnNamePrimaryKeyField']] == $columnsMap['columnValuePrimaryKeyField']) {
                    $this->primaryKey = $column[$columnName];
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getDriversColumnsMap(): array
    {
        return [
            'pdo_pgsql' => [
                'columnNameField' => 'field',
                'columnValueField' => 'default',
                'columnNamePrimaryKeyField' => 'pri',
                'columnValuePrimaryKeyField' => 't',
            ],
            'pdo_mysql' => [
                'columnNameField' => 'Field',
                'columnValueField' => 'Default',
                'columnNamePrimaryKeyField' => 'Key',
                'columnValuePrimaryKeyField' => 'PRI',
            ],
        ];
    }
}