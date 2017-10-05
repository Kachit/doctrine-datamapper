<?php
/**
 * Class Memory
 *
 * @package Kachit\Database\MetaData
 * @author Kachit
 */
namespace Kachit\Database\MetaData;

use Kachit\Database\MetaDataInterface;

class Memory implements MetaDataInterface
{
    /**
     * @var string
     */
    private $primaryKey;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * Memory constructor.
     * @param string $tableName
     * @param string $primaryKey
     * @param array $columns
     */
    public function __construct(string $tableName, string $primaryKey, array $columns)
    {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getPrimaryKeyColumn(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $row
     * @return array
     */
    public function filterRow(array $row): array
    {
        $columns = $this->getColumns();
        foreach ($row as $key => $value) {
            if (!in_array($key, $columns) || is_null($value)) {
                unset($row[$key]);
            }
        }
        return $row;
    }
}