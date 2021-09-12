<?php
use Stubs\DB\Entity;
use Stubs\DB\Mapper;
use Stubs\DB\Gateway;

use Kachit\Database\NullEntity;
use Kachit\Database\Exception\MapperException;
use Kachit\Database\Exception\EntityException;
use Kachit\Database\Query\Filter\Builder;
use Kachit\Database\CollectionInterface;
use Kachit\Database\Collection;
use Kachit\Database\MetaDataInterface;
use Kachit\Database\GatewayInterface;
use Kachit\Database\EntityInterface;
use Kachit\Database\HydratorInterface;
use Kachit\Database\Hydrator;
use Kachit\Database\EntityValidatorInterface;
use Kachit\Database\Entity\Validator;
use Kachit\Database\Mocks\Doctrine\DBAL\ConnectionMock;

class MapperTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Mapper
     */
    protected $testable;

    /**
     * @var ConnectionMock
     */
    protected $connection;

    protected function _before()
    {
        $this->connection = $this->tester->mockDatabase();
        $gateway = new Gateway($this->connection);
        $this->testable = new Mapper($gateway, new Entity());
        $this->connection->reset();
    }

    public function testFetchAll()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);
        $result = $this->testable->fetchAll();
        //data
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertEquals($expected, $result->getFirst()->toArray());
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
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertEquals($expected, $result->getFirst()->toArray());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testFetchAllEmpty()
    {
        $result = $this->testable->fetchAll();
        //data
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertEquals(0, $result->count());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetch()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);
        $result = $this->testable->fetch();
        //data
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertEquals($expected, $result->toArray());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t', $query['query']);
        $this->assertEquals([], $query['params']);
    }

    public function testFetchEmpty()
    {
        $result = $this->testable->fetch();
        //data
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertInstanceOf(NullEntity::class, $result);
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
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertEquals($expected, $result->toArray());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testFetchByPk()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);

        $result = $this->testable->fetchByPk(1);
        //data
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertEquals($expected, $result->toArray());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testCount()
    {
        $this->connection->addFetchResult([[1]]);

        $result = $this->testable->count();
        //data
        $this->assertEquals(1, $result);
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT COUNT(t.*) FROM users t', $query['query']);
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

    public function testDelete()
    {
        $entity = new Entity(['id' => 1]);
        $result = $this->testable->delete($entity);
        //query
        $this->assertCount(1, $this->connection->getUpdates());

        $query = $this->connection->getLastUpdate();
        $this->assertEquals('DELETE FROM users WHERE id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testSaveInsert()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([[]]);

        $entity = new Entity(['name' => 'name', 'email' => 'email', 'active' => true]);
        $result = $this->testable->save($entity);
        $this->assertTrue($result);
        $this->assertEquals($expected, $entity->toArray());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);

        $query = $this->connection->getLastInsert();
        $this->assertEquals('users', $query['table']);
        $this->assertEquals(['name' => 'name', 'email' => 'email', 'active' => true], $query['data']);
        $this->assertEquals(1, $query['last_insert_id']);
    }

    public function testSaveUpdate()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([[]]);

        $entity = new Entity(['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true]);
        $result = $this->testable->save($entity);
        $this->assertTrue($result);
        $this->assertEquals($expected, $entity->toArray());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);

        $query = $this->connection->getLastUpdate();
        $this->assertEquals("UPDATE users t SET name = :name, email = :email, active = :active WHERE t.id = :dcValue1", $query['query']);
        $this->assertEquals(['name' => 'name', 'email' => 'email', 'active' => true, 'dcValue1' => 1], $query['params']);
    }

    public function testSyncEntity()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $this->connection->addFetchResult([$expected]);

        $entity = (new Entity($expected))->setActive(false);
        $this->tester->callNonPublicMethod($this->testable, 'syncEntity', [$entity]);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertInstanceOf(EntityInterface::class, $entity);
        $this->assertEquals($expected, $entity->toArray());
        $this->assertTrue($entity->isActive());
        //query
        $query = $this->connection->getLastQuery();
        $this->assertEquals('SELECT t.* FROM users t WHERE t.id = :dcValue1', $query['query']);
        $this->assertEquals(['dcValue1' => 1], $query['params']);
    }

    public function testHydrateEntity()
    {
        $expected = ['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true];
        $entity = $this->tester->callNonPublicProperty($this->testable, 'entity');
        /* @var Entity $result */
        $result = $this->tester->callNonPublicMethod($this->testable, 'hydrateEntity', [$expected]);
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertEquals($expected, $result->toArray());
        $this->assertNotEquals(spl_object_hash($entity), spl_object_hash($result));
    }

    public function testHydrateCollection()
    {
        $expected = [['id' => 1, 'name' => 'name', 'email' => 'email', 'active' => true]];
        $entity = $this->tester->callNonPublicProperty($this->testable, 'collection');
        /* @var Collection $result */
        $result = $this->tester->callNonPublicMethod($this->testable, 'hydrateCollection', [$expected]);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertEquals($expected[0], $result->getFirst()->toArray());
        $this->assertNotEquals(spl_object_hash($entity), spl_object_hash($result));
    }

    public function testCreateDefaultMetadata()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'createDefaultMetadata');
        $this->assertInstanceOf(MetaDataInterface::class, $result);
    }

    public function testGetTableGateway()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'getTableGateway');
        $this->assertInstanceOf(Gateway::class, $result);
        $this->assertInstanceOf(GatewayInterface::class, $result);
    }

    public function testCreateDefaultValidator()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'createDefaultValidator');
        $this->assertInstanceOf(Validator::class, $result);
        $this->assertInstanceOf(EntityValidatorInterface::class, $result);
    }

    public function testCreateDefaultHydrator()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'createDefaultHydrator');
        $this->assertInstanceOf(Hydrator::class, $result);
        $this->assertInstanceOf(HydratorInterface::class, $result);
    }

    public function testCreateDefaultCollection()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'createDefaultCollection');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(CollectionInterface::class, $result);
    }

    public function testCreateCollection()
    {
        $collection = $this->tester->callNonPublicMethod($this->testable, 'createDefaultCollection');
        $result = $this->tester->callNonPublicMethod($this->testable, 'createCollection');
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(CollectionInterface::class, $result);
        $this->assertNotEquals(spl_object_hash($collection), spl_object_hash($result));
    }

    public function testCreateEntity()
    {
        $entity = $this->tester->callNonPublicProperty($this->testable, 'entity');
        $result = $this->tester->callNonPublicMethod($this->testable, 'createEntity');
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertNotEquals(spl_object_hash($entity), spl_object_hash($result));
    }
}
