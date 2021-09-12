<?php
use Stubs\DB\GatewayInsertRaw;
use Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock;

class GatewayInsertRawTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var GatewayInsertRaw
     */
    protected $testable;

    /**
     * @var ConnectionMock
     */
    protected $connection;

    protected function _before()
    {
        $this->connection = $this->tester->mockDatabase();
        $this->testable = new GatewayInsertRaw($this->connection);
        $this->connection->reset();
    }

    public function testInsert()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $result = $this->testable->insert($expected);
        //query
        $this->assertCount(1, $this->connection->getInserts());

        $query = $this->connection->getLastInsert();
        $this->assertEquals('users', $query['table']);
        $this->assertEquals($expected, $query['data']);
        $this->assertEquals($result, $query['last_insert_id']);
    }
}
