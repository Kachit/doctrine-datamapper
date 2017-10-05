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
    /**
     * @return string
     */
    public function getPrimaryKey(): string;

    /**
     * @return array
     */
    public function getColumns(): array;

    /**
     * @return void
     */
    public function initialize();
}