<?php
use Kachit\Database\GenericEntity as Entity;

class GenericEntityTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCreateEntity()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity = new Entity($array);
        $this->assertEquals($array['id'], $entity->getId());
        $this->assertEquals($array['name'], $entity->getName());
        $this->assertEquals($array['id'], $entity->id);
        $this->assertEquals($array['name'], $entity->name);
        $this->assertEquals($array, $entity->toArray());
        $this->assertEquals(json_encode($array), json_encode($entity));
        $entity->id = 2;
        $this->assertEquals('bar', $entity->setName('bar')->getName());
        $this->assertEquals(2, $entity->getId());
    }
}
