<?php
/**
 * Class MetaDataInterface
 *
 * @package Kachit\Database
 * @author Kachit
 */
namespace Kachit\Database;

interface MetaDataInterface
{
    const DEFAULT_PRIMARY_KEY = 'id';

    /**
     * @return string
     */
    public function getPrimaryKeyColumn(): string;

    /**
     * @return array
     */
    public function getColumns(): array;

    /**
     * @param array $data
     * @return array
     */
    public function filterRowForInsert(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function filterRowForUpdate(array $data): array;
}
