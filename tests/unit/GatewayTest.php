<?php
use Stubs\DB\Gateway;
use Mocks\DBALConnectionMock;
use Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock;

class GatewayTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Gateway
     */
    protected $testable;

    /**
     * @var ConnectionMock
     */
    protected $connection;

    protected function _before()
    {
        $this->connection = $this->tester->mockDatabase();
        $this->testable = new Gateway($this->connection);
    }

    public function testGetTableName()
    {
        $result = $this->testable->getTableName();
        $this->assertEquals('users', $result);
    }

    protected function testGetDefaultCacheProfile()
    {
        $testable = $this->testable;
        $method = function ($lifetime) {
            return $this->getDefaultCacheProfile($lifetime);
        };
        $bind = $method->bindTo($testable, $testable);
        $result = $bind(10);
        $this->assertEmpty($result);
    }
}
