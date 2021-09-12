<?php
use Stubs\DB\GatewaySoftDelete;
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Filter\Builder;
use Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock;

class GatewaySoftDeleteTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var GatewaySoftDelete
     */
    protected $testable;

    /**
     * @var ConnectionMock
     */
    protected $connection;

    protected function _before()
    {
        $this->connection = $this->tester->mockDatabase();
        $this->testable = new GatewaySoftDelete($this->connection);
        $this->connection->reset();
    }

    public function testGetSoftDeleteCondition()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'getSoftDeleteCondition');
        $this->assertEquals(['active' => 'false'], $result);
    }

    public function testDelete()
    {
        $result = $this->testable->delete();
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals("UPDATE users t SET active = 'false'", $query['query']);
        $this->assertEquals([], $query['params']);
    }
}
