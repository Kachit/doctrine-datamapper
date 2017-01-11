<?php
use Kachit\Database\NullEntity as Entity;

class NullEntityTest extends \Codeception\Test\Unit {

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
        $this->assertEquals(null, $entity->getName());
        $this->assertEquals([], $entity->toArray());
        $this->assertEquals(json_encode(new \StdClass()), json_encode($entity));
        $this->assertEquals(null, $entity->setId(2)->getPk());
    }
}