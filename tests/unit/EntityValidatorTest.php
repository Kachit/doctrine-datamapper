<?php

use Kachit\Database\Exception\EntityException;
use Kachit\Database\NullEntity;
use Stubs\DB\Entity as StubEntity;
use Kachit\Database\Validator as EntityValidator;

class EntityValidatorTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var EntityValidator
     */
    protected $testable;

    protected function _before()
    {
        $this->testable = new EntityValidator(new StubEntity());
    }

    /**
     *
     */
    public function testValidateSuccess()
    {
        $this->testable->validate(new StubEntity(), 'id');
    }

    /**
     *
     */
    public function testValidateNullEntity()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(sprintf('Entity "%s" is null', NullEntity::class));
        $this->testable->validate(new NullEntity(), 'id');
    }

    /**
     *
     */
    public function testValidateWrongEntity()
    {
        $this->expectException(EntityException::class);
        //$this->expectExceptionMessage(sprintf('Entity "%s" is not valid', NullEntity::class));
        $this->testable->validate($this->getEntityMock(), 'id');
    }

    /**
     *
     */
    public function testValidateEntityWithoutPrimaryKey()
    {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(sprintf('Entity "%s" has not primary key field', StubEntity::class));
        $this->testable->validate(new StubEntity(), 'foo');
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
