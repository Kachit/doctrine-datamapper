<?php
use Stubs\DB\Gateway;
use Mocks\DBALConnectionMock;

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
     *
     */
    protected function _before()
    {
        $this->testable = new Gateway((new DBALConnectionMock())->createObject()->withExpressionBuilder()->get());
    }

    /**
     *
     */
    public function testGetTableName()
    {
        $result = $this->testable->getTableName();
        $this->assertEquals('users', $result);
    }

    /**
     *
     */
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