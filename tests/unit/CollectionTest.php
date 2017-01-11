<?php
use Stubs\DB\Entity;

use Kachit\Database\Collection;
use Kachit\Database\NullEntity;

class CollectionTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testCollection()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity = new Entity($array);
        $collection = new Collection();
        $collection->add($entity);
        $this->assertFalse($collection->isEmpty());
        $this->assertEquals(1, $collection->count());
        $this->assertTrue($collection->has(1));
        $this->assertEquals($entity, $collection->get(1));
        $this->assertEquals([1 => $entity], $collection->toArray());
        $this->assertEquals(json_encode([1 => $array]), json_encode($collection));
        $this->assertTrue($collection->remove(1)->isEmpty());
    }

    /**
     *
     */
    public function testAddNullEntity()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Entity is null');
        $entity = new NullEntity();
        $collection = new Collection();
        $collection->add($entity);
    }

    /**
     *
     */
    public function testGetNonExistingEntity()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Entity with index "1" is not exists');
        $collection = new Collection();
        $collection->get(1);
    }

    /**
     *
     */
    public function testRemoveNonExistingEntity()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Entity with index "1" is not exists');
        $collection = new Collection();
        $collection->remove(1);
    }

    /**
     *
     */
    public function testAddEntityWithoutPK()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Entity has no primary key');
        $entity = new Entity();
        $collection = new Collection();
        $collection->add($entity);
    }
}