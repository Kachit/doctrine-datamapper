<?php
/**
 * Class MocksFactory
 *
 * @package Helper
 * @author Kachit
 */
namespace Helper;

use Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock;
use Kachit\Database\Mocks\Doctrine\DBAL\DriverMock;
use Codeception\Module;

class MocksFactory extends Module
{
    /**
     * @return \Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock
     */
    public function mockDatabase(): ConnectionMock
    {
        $connection = new ConnectionMock([], new DriverMock());
        return $connection;
    }

}