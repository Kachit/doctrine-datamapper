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
     * @param array $array
     * @return array
     */
    public function filterRow(array $array): array;
}