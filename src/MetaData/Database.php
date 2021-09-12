<?php
/**
 * Metadata Database class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\MetaData;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;

class Database extends AbstractMetadata
{
    /**
     * @var bool
     */
    private $initialized_primary_key = false;

    /**
     * @var bool
     */
    private $initialized_columns = false;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * MetaData constructor
     *
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
        $this->initializeColumns();
        return array_keys(parent::getColumns());
    }

    /**
     * @return void
     */
    protected function initializeColumns()
    {
        if (empty($this->initialized_columns)) {
            $columns = $this->connection->getSchemaManager()->listTableColumns($this->tableName);
            $this->extractColumnsData($columns);
            $this->initialized_columns = true;
        }
    }

    /**
     * @return void
     */
    protected function initializePrimaryKey()
    {
        if (empty($this->initialized_primary_key)) {
            $indexes = $this->connection->getSchemaManager()->listTableIndexes($this->tableName);
            $this->extractIndexesData($indexes);
            $this->initialized_primary_key = true;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKeyColumn(): string
    {
        $this->initializePrimaryKey();
        return parent::getPrimaryKeyColumn();
    }

    /**
     * @param Column[] $columns
     */
    private function extractColumnsData(array $columns)
    {
        foreach ($columns as $column) {
            $this->columns[$column->getName()] = $column;
        }
    }

    /**
     * @param Index[] $indexes
     */
    private function extractIndexesData(array $indexes)
    {
        foreach ($indexes as $index) {
            if ($index->isPrimary()) {
                $columns = $index->getColumns();
                $this->primaryKey = array_shift($columns);
                break;
            }
        }
    }
}
