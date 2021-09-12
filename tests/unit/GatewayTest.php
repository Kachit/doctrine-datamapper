<?php
use Stubs\DB\Gateway;
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Filter\Builder;
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
        $this->connection->reset();
    }

    public function testGetTableName()
    {
        $result = $this->testable->getTableName();
        $this->assertEquals('users', $result);
    }

    public function testGetTableAlias()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'getTableAlias');
        $this->assertEquals('t', $result);
    }

    public function testGetTableFields()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'getTableFields');
        $this->assertEquals('t.*', $result);
    }

    public function testBuildPrimaryKeyFilter()
    {
        /* @var Filter $result */
        $result = $this->tester->callNonPublicMethod($this->testable, 'buildPrimaryKeyFilter', ['id', 1]);
        $condition = $result->getCondition('id', Filter::OPERATOR_IS_EQUAL);
        $this->assertInstanceOf(Filter::class, $result);
        $this->assertEquals(1, $condition->getValue());
    }

    public function testSetConnection()
    {
        $this->testable->setConnection($this->tester->mockDatabase());
        $this->assertNotEquals(spl_object_hash($this->connection), spl_object_hash($this->testable->getConnection()));
    }

    public function testFetchAll()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);
        $result = $this->testable->fetchAll();
        //data
        $this->assertEquals($expected, $result[0]);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetchAllEmpty()
    {
        $result = $this->testable->fetchAll();
        //data
        $this->assertEmpty($result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetchAllWithFilter()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);

        $filter = (new Builder())->eq('id', 1)->getFilter();
        $result = $this->testable->fetchAll($filter);
        //data
        $this->assertEquals($expected, $result[0]);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testFetch()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);
        $result = $this->testable->fetch();
        //data
        $this->assertEquals($expected, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetchEmpty()
    {
        $result = $this->testable->fetch();
        //data
        $this->assertEmpty($result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetchWithFilter()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);

        $filter = (new Builder())->eq('id', 1)->getFilter();
        $result = $this->testable->fetch($filter);
        //data
        $this->assertEquals($expected, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testFetchByPK()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);

        $result = $this->testable->fetchByPk(1);
        //data
        $this->assertEquals($expected, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testFetchColumn()
    {
        $this->connection->addFetchResult([[123]]);
        $result = $this->testable->fetchColumn('COUNT(*)');
        //data
        $this->assertEquals(123, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(*) FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetchColumnWithFilter()
    {
        $this->connection->addFetchResult([[123]]);

        $filter = (new Builder())->eq('id', 1)->getFilter();
        $result = $this->testable->fetchColumn('COUNT(*)', $filter);
        //data
        $this->assertEquals(123, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(*) FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testFetchColumnEmpty()
    {
        $result = $this->testable->fetchColumn('COUNT(*)');
        //data
        $this->assertEmpty($result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(*) FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testCount()
    {
        $this->connection->addFetchResult([[123]]);
        $result = $this->testable->count();
        //data
        $this->assertEquals(123, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(t.*) FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testCountWithColumn()
    {
        $this->connection->addFetchResult([[123]]);
        $result = $this->testable->count(null, 'id');
        //data
        $this->assertEquals(123, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(t.id) FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testCountWithFilter()
    {
        $this->connection->addFetchResult([[123]]);

        $filter = (new Builder())->eq('id', 1)->getFilter();
        $result = $this->testable->count($filter);
        //data
        $this->assertEquals(123, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(t.*) FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
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

    public function testUpdate()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $result = $this->testable->update($expected);
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals('UPDATE users t SET id = :id, name = :name, email = :email, active = :active', $query['query']);
        $this->assertEquals(['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true], $query['params']);
    }

    public function testUpdateByFilter()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $filter = (new Builder)->eq('id', 1)->getFilter();
        $result = $this->testable->update($expected, $filter);
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals("UPDATE users t SET id = :id, name = :name, email = :email, active = :active WHERE t.id = :dcValue1", $query['query']);
        $this->assertEquals(['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true, 'dcValue1' => 1], $query['params']);
    }

    public function testUpdateByPk()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $result = $this->testable->updateByPk($expected, 1);
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals("UPDATE users t SET id = :id, name = :name, email = :email, active = :active WHERE t.id = :dcValue1", $query['query']);
        $this->assertEquals(['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true, 'dcValue1' => 1], $query['params']);
    }

    public function testDelete()
    {
        $result = $this->testable->delete();
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals('DELETE FROM users', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testDeleteByFilter()
    {
        $filter = (new Builder)->eq('id', 1)->getFilter();
        $result = $this->testable->delete($filter);
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals('DELETE FROM users WHERE id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testDeleteByPk()
    {
        $result = $this->testable->deleteByPk(1);
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals('DELETE FROM users WHERE id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }
}
