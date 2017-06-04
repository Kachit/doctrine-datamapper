<?php
use Stubs\DB\Entity;
use Kachit\Database\Mapper;
use Stubs\DB\Gateway;
use Kachit\Database\NullEntity;
use Kachit\Database\Exception\MapperException;
use Kachit\Database\CollectionInterface;

class MapperTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testFetchExistsData()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $gateway = $this->getGatewayMock('fetch', $array);
        $mapper = new Mapper($gateway, new Entity());
        /* @var Entity $entity */
        $entity = $mapper->fetch();
        $this->assertNotEmpty($entity);
        $this->assertTrue(is_object($entity));
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertFalse($entity->isNull());
        $this->assertEquals($entity->getId(), $entity->getPk());
        $this->assertEquals('foo', $entity->getName());
        $this->assertEquals($array, $entity->toArray());
        $this->assertEquals(json_encode($array), json_encode($entity));
        $this->assertEquals(2, $entity->setId(2)->getPk());
    }

    /**
     *
     */
    public function testFetchNotExistsData()
    {
        $array = [];
        $gateway = $this->getGatewayMock('fetch', $array);
        $mapper = new Mapper($gateway, new Entity());
        /* @var Entity $entity */
        $entity = $mapper->fetch();
        $this->assertNotEmpty($entity);
        $this->assertTrue(is_object($entity));
        $this->assertInstanceOf(NullEntity::class, $entity);
        $this->assertTrue($entity->isNull());
        $this->assertEquals($entity->getId(), $entity->getPk());
        $this->assertEquals(null, $entity->getName());
        $this->assertEquals($array, $entity->toArray());
        $this->assertEquals(json_encode((object)$array), json_encode($entity));
        $this->assertEquals(null, $entity->setId(2)->getPk());
    }

    /**
     *
     */
    public function testFetchAllExistsData()
    {
        $array = [['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1], ['id' => 2, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]];
        $gateway = $this->getGatewayMock('fetchAll', $array);
        $mapper = new Mapper($gateway, new Entity());
        $collection = $mapper->fetchAll();
        $this->assertNotEmpty($collection);
        $this->assertTrue(is_object($collection));
        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertEquals(2, $collection->count());
        $this->assertTrue($collection->has(1));
        $this->assertTrue($collection->has(2));
    }

    /**
     *
     */
    public function testFetchAllNotExistsData()
    {
        $array = [];
        $gateway = $this->getGatewayMock('fetchAll', $array);
        $mapper = new Mapper($gateway, new Entity());
        $collection = $mapper->fetchAll();
        $this->assertNotEmpty($collection);
        $this->assertTrue(is_object($collection));
        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertEquals(0, $collection->count());
    }

    /**
     *
     */
    public function testSaveNullEntity()
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Entity "Kachit\Database\NullEntity" is null');
        $array = [];
        $gateway = $this->getGatewayMock('fetchAll', $array);
        $entity = new NullEntity();
        $mapper = new Mapper($gateway, new Entity());
        $mapper->save($entity);
    }

    /**
     *
     */
    public function testSaveWrongEntity()
    {
        $this->expectException(MapperException::class);
        $array = [];
        $gateway = $this->getGatewayMock('fetchAll', $array);
        $entity = $this->getEntityMock();
        $mapper = new Mapper($gateway, new Entity());
        $mapper->save($entity);
    }

    /**
     * @param $method
     * @param array $result
     * @return PHPUnit_Framework_MockObject_MockObject| Gateway
     */
    private function getGatewayMock($method, array $result = [])
    {
        $source = $this->getMockBuilder(Gateway::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $source->method($method)->willReturn($result);
        return $source;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject| NullEntity
     */
    private function getEntityMock()
    {
        $source = $this->getMockBuilder(NullEntity::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $source->method('isNull')->willReturn(false);
        return $source;
    }
}