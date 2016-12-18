<?php
namespace Kachit\Silex\Database\Meta;

use Doctrine\DBAL\Connection;

class Table
{
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
     * MetaData constructor.
     * @param Connection $connection
     * @param $table
     */
    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
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
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
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
            $this->columns[] = $column['Field'];
            if ($column['Key'] == 'PRI' && empty($this->primaryKey)) {
                $this->primaryKey = $column['Field'];
            }
        }
        return $result;
    }
}