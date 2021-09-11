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
use Stubs\DB\Entity as StubEntity;

class Mapper extends BaseMapper
{
    /**
     * @return MetaDataInterface
     */
    protected function createDefaultMetadata(): MetaDataInterface
    {
        $entity = new StubEntity();
        return new Memory('users', 'id', array_keys($entity->toArray()));
    }
}
