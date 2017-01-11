<?php
use Codeception\Util\Debug;
use Kachit\Database\Hydrator;
use Stubs\DB\Entity;

class HydratorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testHydrate()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $hydrator = new Hydrator();
        /* @var Entity $result */
        $result = $hydrator->hydrate($array, new Entity());
        $entity = new Entity($array);
        $this->assertNotEmpty($result);
        $this->assertTrue(is_object($result));
        $this->assertInstanceOf('Stubs\DB\Entity', $result);
        $this->assertInstanceOf('Kachit\Database\EntityInterface', $result);
        $this->assertEquals($entity->getId(), $result->getId());
        $this->assertEquals($entity->getPk(), $result->getPk());
        $this->assertEquals($entity->getName(), $result->getName());
    }

    /**
     *
     */
    public function testHydrateEmpty()
    {
        $array = [];
        $hydrator = new Hydrator();
        /* @var Entity $result */
        $result = $hydrator->hydrate($array, new Entity());
        $entity = new Entity($array);
        $this->assertNotEmpty($result);
        $this->assertTrue(is_object($result));
        $this->assertInstanceOf('Kachit\Database\EntityInterface', $result);
        $this->assertInstanceOf('Kachit\Database\NullEntity', $result);
        $this->assertEquals($entity->getId(), $result->getId());
        $this->assertEquals($entity->getPk(), $result->getPk());
        $this->assertEquals($entity->getName(), $result->getName());
    }

    /**
     *
     */
    public function testExtract()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity = new Entity($array);
        $hydrator = new Hydrator();
        $result = $hydrator->extract($entity);
        $this->assertEquals($array, $result);
        $this->assertEquals($entity->toArray(), $result);
    }
}