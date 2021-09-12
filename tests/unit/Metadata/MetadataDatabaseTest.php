<?php
use Kachit\Database\MetaData\Database as MetaDataDatabase;
use Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock;

class MetadataDatabaseTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var MetaDataDatabase
     */
    protected $testable;

    /**
     * @var ConnectionMock
     */
    protected $connection;

    protected function _before()
    {
        $this->connection = $this->tester->mockDatabase();
        $this->testable = new MetaDataDatabase($this->connection, 'users');
        $this->connection->reset();
    }

    protected function testGetPrimaryKeyColumn()
    {
        $this->connection->addFetchResult([
            [
                'indrelid' => 1,
                'idkey' => 1,
            ],
            [
                'indrelid' => 2,
                'idkey' => 2,
            ],
        ]);
        $this->assertEquals('id', $this->testable->getPrimaryKeyColumn());
    }

    public function testGetColumns()
    {
        $this->connection->addFetchResult([
            [
                'field' => 'id',
                'pri' => 't',
            ],
            [
                'field' => 'name',
                'pri' => '',
            ],
            [
                'field' => 'email',
                'pri' => '',
            ],
            [
                'field' => 'active',
                'pri' => '',
            ],
        ]);
        $this->assertEquals(['id', 'name', 'email', 'active'], $this->testable->getColumns());
    }
}
