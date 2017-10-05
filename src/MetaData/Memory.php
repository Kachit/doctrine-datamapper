<?php
/**
 * Class Memory
 *
 * @package Kachit\Database\MetaData
 * @author Kachit
 */
namespace Kachit\Database\MetaData;

class Memory extends AbstractMetadata
{
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
}