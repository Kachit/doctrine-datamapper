<?php
/**
 * Class SchemaManagerMock
 *
 * @package Kachit\Database\Mocks\Doctrine\DBAL
 * @author Kachit
 */
namespace Kachit\Database\Mocks\Doctrine\DBAL;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

class SchemaManagerMock extends \Doctrine\DBAL\Schema\AbstractSchemaManager
{
    public function __construct(\Doctrine\DBAL\Connection $conn)
    {
        parent::__construct($conn);
    }

    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $column = new Column($tableColumn['field'], Type::getType('string'), []);
        return $column;
    }
}
