<?php
namespace Kachit\Silex\Database\Tests\Testable;

use Kachit\Silex\Database\Gateway as AbstractGateway;

class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'posts';
    }
}