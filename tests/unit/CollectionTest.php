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

    public function testAdd()
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

    public function testFill()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity = new Entity($array);
        $collection = new Collection([$entity]);
        $this->assertFalse($collection->isEmpty());
        $this->assertEquals(1, $collection->count());
        $this->assertTrue($collection->has(0));
        $this->assertEquals($entity, $collection->get(0));
        $this->assertEquals([$entity], $collection->toArray());
        $this->assertEquals(json_encode([$array]), json_encode($collection));
        $this->assertTrue($collection->remove(0)->isEmpty());
    }

    public function testGetFirstSuccess()
    {
        $array1 = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity1 = new Entity($array1);
        $array2 = ['id' => 2, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity2 = new Entity($array2);

        $collection = new Collection();
        $collection->add($entity1)->add($entity2);
        $this->assertEquals($entity1, $collection->getFirst());
        $this->assertEquals($entity1, $collection->getFirst());
    }

    public function testGetFirstError()
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Collection is empty');
        $collection = new Collection();
        $collection->getFirst();
    }

    public function testGetLastSuccess()
    {
        $array1 = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity1 = new Entity($array1);
        $array2 = ['id' => 2, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity2 = new Entity($array2);

        $collection = new Collection();
        $collection->add($entity1)->add($entity2);
        $this->assertEquals($entity2, $collection->getLast());
        $this->assertEquals($entity2, $collection->getLast());
    }

    public function testGetLatError()
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Collection is empty');
        $collection = new Collection();
        $collection->getLast();
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

    public function testGetNonExistingEntity()
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Entity with index "1" is not exists');
        $collection = new Collection();
        $collection->get(1);
    }

    public function testRemoveNonExistingEntity()
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('Entity with index "1" is not exists');
        $collection = new Collection();
        $collection->remove(1);
    }

    public function testClear()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $this->assertFalse($collection->isEmpty());
        $collection->clear();
        $this->assertTrue($collection->isEmpty());
    }

    public function testAppend()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $col = (new Collection())->append($collection);
        $this->assertFalse($col->isEmpty());
        $this->assertCount(2, $col);
    }

    public function testCount()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $this->assertCount(2, $collection);
        $this->assertEquals(2, $collection->count());
    }

    public function testSliceWithOffsetWithoutLimit()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 3, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 4, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 5, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 6, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 7, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->slice(3);
        $this->assertEquals(4, $result->count());
        $this->assertEquals(4, $result->getFirst()->getId());
        $this->assertEquals(7, $result->getLast()->getId());
    }

    public function testSliceWithOffsetAndLimit()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 3, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 4, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 5, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 6, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 7, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->slice(3, 3);
        $this->assertEquals(3, $result->count());
        $this->assertEquals(4, $result->getFirst()->getId());
        $this->assertEquals(6, $result->getLast()->getId());
    }

    public function testWalk()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->walk(function (Entity $entity) {
            return $entity->setId($entity->getId() * 3);
        });
        $this->assertEquals(3, $result->getFirst()->getId());
        $this->assertEquals(6, $result->getLast()->getId());
    }
}
