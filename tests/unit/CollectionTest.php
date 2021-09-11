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
        $this->assertEquals(spl_object_hash($collection), spl_object_hash($result));
        $this->assertEquals(3, $result->getFirst()->getId());
        $this->assertEquals(6, $result->getLast()->getId());
    }

    public function testMap()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->map(function (Entity $entity) {
            return $entity->setId($entity->getId() * 3);
        });
        $this->assertNotEquals(spl_object_hash($collection), spl_object_hash($result));
        $this->assertEquals(3, $result->getFirst()->getId());
        $this->assertEquals(6, $result->getLast()->getId());
    }

    public function testFilter()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 3, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 4, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->filter(function (Entity $entity) {
            return $entity->getId() % 2;
        });
        $this->assertEquals(1, $result->getFirst()->getId());
        $this->assertEquals(3, $result->getLast()->getId());
    }

    public function testSort()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 4, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 1, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 5, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->sort(function (Entity $firstObject, Entity $secondObject) {
            if ($firstObject->getId() == $secondObject->getId()) {
                return 0;
            }
            return ($firstObject->getId() < $secondObject->getId()) ? -1 : 1;
        });
        $this->assertEquals(1, $result->getFirst()->getId());
        $this->assertEquals(5, $result->getLast()->getId());
    }

    public function testGetKeys()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 3, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 4, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = $collection->getKeys();
        $this->assertEquals([0, 1, 2, 3], $result);
    }

    public function testCloneObject()
    {
        $collection = new Collection();
        $entity = new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]);
        $collection->add($entity);

        $result = $collection->cloneObject(0);
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertEquals($entity->toArray(), $result->toArray());
        $this->assertNotEquals(spl_object_hash($entity), spl_object_hash($result));
    }

    public function testClone()
    {
        $collection = new Collection();
        $collection->add(new Entity(['id' => 1, 'name' => 'foo1', 'email' => 'foo1@bar', 'active' => 1]));
        $collection->add(new Entity(['id' => 2, 'name' => 'foo2', 'email' => 'foo2@bar', 'active' => 1]));

        $result = clone $collection;
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($collection->toArray(), $result->toArray());
        $this->assertNotEquals(spl_object_hash($collection), spl_object_hash($result));
        $this->assertEquals($collection->get(0)->toArray(), $result->get(0)->toArray());
        $this->assertNotEquals(spl_object_hash($collection->get(0)), spl_object_hash($result->get(0)));
    }
}
