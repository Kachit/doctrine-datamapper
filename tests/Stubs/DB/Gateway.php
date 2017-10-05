<?php
/**
 * Class Gateway
 *
 * @package Stubs\DB
 * @author Kachit
 */
namespace Stubs\DB;

use Kachit\Database\Gateway as AbstractGateway;

class Gateway extends AbstractGateway
{
    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return 'users';
    }
}