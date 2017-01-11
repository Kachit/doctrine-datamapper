<?php
use Stubs\DB\Entity;

class EntityTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testCreateEntity()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity = new Entity($array);
        $this->assertEquals($entity->getId(), $entity->getPk());
        $this->assertEquals('foo', $entity->getName());
        $this->assertEquals($array, $entity->toArray());
        $this->assertEquals(json_encode($array), json_encode($entity));
        $this->assertEquals(2, $entity->setId(2)->getPk());
    }
}