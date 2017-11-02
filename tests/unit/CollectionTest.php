<?php
use Stubs\DB\Entity;

use Kachit\Database\Collection;
use Kachit\Database\NullEntity;
use Kachit\Database\Exception\CollectionException;

class CollectionTest extends \Codeception\Test\Unit
{
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
        $this->assertTrue($collection->has(0));
        $this->assertEquals($entity, $collection->get(0));
        $this->assertEquals([$entity], $collection->toArray());
        $this->assertEquals(json_encode([$array]), json_encode($collection));
        $this->assertTrue($collection->remove(0)->isEmpty());
    }

    public function testExtractValue()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $result = $collection->extract('name');
        $this->assertEquals(['foo1', 'foo2'], $result);

    }

    public function testExtractKeyValue()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $result = $collection->extract('name', 'id');
        $this->assertEquals([1 => 'foo1', 2 => 'foo2'], $result);

    }

    /**
     *
     */
    public function testGetNonExistingEntity()
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Entity with index "1" is not exists');
        $collection = new Collection();
        $collection->get(1);
    }

    /**
     *
     */
    public function testRemoveNonExistingEntity()
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Entity with index "1" is not exists');
        $collection = new Collection();
        $collection->remove(1);
    }
}