<?php
/**
 * Abstract Metadata class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\MetaData;

use Kachit\Database\MetaDataInterface;

abstract class AbstractMetadata implements MetaDataInterface
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @var array
     */
    protected $columns = [];

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
            $data[$key] = $this->convertValue($value);
        }
        return $data;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function convertValue($value)
    {
        if ($value === false) {
            $value = 'false';
        }
        return $value;
    }
}