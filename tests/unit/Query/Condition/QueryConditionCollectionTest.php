<?php
use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\Condition;
use Kachit\Database\Query\Condition\Collection;

class QueryConditionCollectionTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Collection
     */
    protected $testable;

    /**
     *
     */
    protected function _before()
    {
        $this->testable = new Collection();
    }

    /**
     *
     */
    public function testAdd()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->testable->add($condition);
        $this->assertFalse($this->testable->isEmpty());
    }

    /**
     *
     */
    public function testGetByField()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->assertEmpty($this->testable->getByField('foo'));
        $this->testable->add($condition);
        $result = $this->testable->getByField('foo');
        $this->assertNotEmpty($result);

        $condition = $result[FilterInterface::OPERATOR_IS_EQUAL];
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(FilterInterface::OPERATOR_IS_EQUAL, $condition->getOperator());
    }

    /**
     *
     */
    public function testHasByField()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->assertFalse($this->testable->hasByField('foo'));
        $this->testable->add($condition);
        $this->assertTrue($this->testable->hasByField('foo'));
    }

    /**
     *
     */
    public function testRemoveByField()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->testable->add($condition);
        $this->assertTrue($this->testable->hasByField('foo'));
        $this->testable->removeByField('foo');
        $this->assertFalse($this->testable->hasByField('foo'));
    }

    /**
     *
     */
    public function testGetByFieldAndOperator()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->assertEmpty($this->testable->getByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
        $this->testable->add($condition);
        $condition = $this->testable->getByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL);
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(FilterInterface::OPERATOR_IS_EQUAL, $condition->getOperator());
        $this->assertEmpty($this->testable->getByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_NOT_EQUAL));
    }

    /**
     *
     */
    public function testHasByFieldAndOperator()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->assertFalse($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
        $this->testable->add($condition);
        $this->assertTrue($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
        $this->assertFalse($this->testable->hasByFieldAndOperator('foo1', FilterInterface::OPERATOR_IS_EQUAL));
        $this->assertFalse($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_NOT_EQUAL));
    }

    /**
     *
     */
    public function testRemoveByFieldAndOperator()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->testable->add($condition);
        $this->assertTrue($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
        $this->testable->removeByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL);
        $this->assertFalse($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
    }

    public function testClear()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->testable->add($condition);
        $this->assertFalse($this->testable->isEmpty());
        $this->assertTrue($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
        $this->testable->clear();
        $this->assertTrue($this->testable->isEmpty());
        $this->assertFalse($this->testable->hasByFieldAndOperator('foo', FilterInterface::OPERATOR_IS_EQUAL));
    }

    public function testToArray()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->testable->add($condition);
        $result = $this->testable->toArray();
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('foo', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(FilterInterface::OPERATOR_IS_EQUAL, $result['foo']);
        $this->assertCount(1, $result['foo']);

        $condition = $result['foo'][FilterInterface::OPERATOR_IS_EQUAL];
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(FilterInterface::OPERATOR_IS_EQUAL, $condition->getOperator());
    }

    /**
     *
     */
    public function testGetIterator()
    {
        $iterator = $this->testable->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertEquals(0, $iterator->count());
    }
}
