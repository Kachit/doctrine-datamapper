<?php

use Kachit\Database\Exception\EntityException;
use Stubs\DB\Entity;

class EntityTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCreateEntity()
    {
        $array = ['id' => 1, 'name' => 'foo', 'email' => 'foo@bar', 'active' => 1];
        $entity = new Entity($array);
        $this->assertEquals($entity->getId(), $entity->getPk());
        $this->assertEquals('foo', $entity->getName());
        $this->assertEquals($array, $entity->toArray());
        $this->assertEquals(json_encode($array), json_encode($entity));
        $this->assertEquals(2, $entity->setId(2)->getPk());
        $entity->setEntityField('id', 3);
        $this->assertEquals(3, $entity->getEntityField('id'));
    }

    public function testGetEntityFieldNotExists()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage('Property "foo" is not exists');
        (new Entity())->getEntityField('foo');
    }

    public function testSetEntityFieldNotExists()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage('Property "foo" is not exists');
        (new Entity())->setEntityField('foo', 'bar');
    }
}
