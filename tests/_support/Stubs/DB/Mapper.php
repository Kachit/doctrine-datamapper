<?php
/**
 * Class Mapper
 *
 * @package Stubs\DB
 * @author Kachit
 */
namespace Stubs\DB;

use Kachit\Database\Mapper as BaseMapper;
use Kachit\Database\MetaData\Memory;
use Kachit\Database\MetaDataInterface;

class Mapper extends BaseMapper
{
    /**
     * @return MetaDataInterface
     */
    protected function createDefaultMetadata(): MetaDataInterface
    {
        return new Memory('users', 'id', []);
    }
}