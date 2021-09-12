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
    public function filterRowForInsert(array $data): array
    {
        $columns = $this->getColumns();
        foreach ($data as $key => $value) {
            if (!in_array($key, $columns) || is_null($value)) {
                unset($data[$key]);
            } else {
                $data[$key] = $this->convertValue($value);
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterRowForUpdate(array $data): array
    {
        $columns = $this->getColumns();
        foreach ($data as $key => $value) {
            if (!in_array($key, $columns) || ($key === $this->getPrimaryKeyColumn())) {
                unset($data[$key]);
            } else {
                $data[$key] = $this->convertValue($value);
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @deprecated
     * @codeCoverageIgnore
     */
    public function filterRow(array $data): array
    {
        return $this->filterRowForInsert($data);
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
