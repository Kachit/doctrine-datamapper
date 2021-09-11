<?php
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Condition;

class QueryFilterTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Filter
     */
    protected $testable;

    protected function _before()
    {
        $this->testable = new Filter();
    }

    public function testCreateCondition()
    {
        $this->testable
            ->createCondition('active', 1)
            ->createCondition('id', [1, 2, 3], Filter::OPERATOR_IS_IN)
        ;
        $this->assertTrue($this->testable->hasCondition('active', Filter::OPERATOR_IS_EQUAL));
        $this->assertTrue($this->testable->hasCondition('id', Filter::OPERATOR_IS_IN));
        $condition = $this->testable->getCondition('active', Filter::OPERATOR_IS_EQUAL);
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('active', $condition->getField());
        $this->assertEquals(Filter::OPERATOR_IS_EQUAL, $condition->getOperator());
        $condition = $this->testable->getCondition('id', Filter::OPERATOR_IS_IN);
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals([1, 2, 3], $condition->getValue());
        $this->assertEquals('id', $condition->getField());
        $this->assertEquals(Filter::OPERATOR_IS_IN, $condition->getOperator());
    }

    public function testAddCondition()
    {
        $this->testable->addCondition(new Condition('active', Filter::OPERATOR_IS_EQUAL, 1));
        $this->assertTrue($this->testable->hasCondition('active', Filter::OPERATOR_IS_EQUAL));
        $condition = $this->testable->getCondition('active', Filter::OPERATOR_IS_EQUAL);
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('active', $condition->getField());
        $this->assertEquals(Filter::OPERATOR_IS_EQUAL, $condition->getOperator());
    }

    public function testGetConditionsByField()
    {
        $this->testable
            ->createCondition('foo', 1, Filter::OPERATOR_IS_GREATER_THAN)
            ->createCondition('foo', 5, Filter::OPERATOR_IS_LESS_THAN)
        ;
        $result = $this->testable->getConditionsByField('foo');
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey(Filter::OPERATOR_IS_GREATER_THAN, $result);
        $this->assertArrayHasKey(Filter::OPERATOR_IS_LESS_THAN, $result);

        $condition = $result[Filter::OPERATOR_IS_GREATER_THAN];
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(Filter::OPERATOR_IS_GREATER_THAN, $condition->getOperator());

        $condition = $result[Filter::OPERATOR_IS_LESS_THAN];
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(5, $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(Filter::OPERATOR_IS_LESS_THAN, $condition->getOperator());
    }

    public function testHasConditionsByField()
    {
        $this->testable
            ->createCondition('foo', 1, Filter::OPERATOR_IS_GREATER_THAN)
            ->createCondition('foo', 5, Filter::OPERATOR_IS_LESS_THAN)
        ;
        $this->assertTrue($this->testable->hasConditionsByField('foo'));
    }

    public function testRemoveConditionsByField()
    {
        $this->testable
            ->createCondition('foo', 1, Filter::OPERATOR_IS_GREATER_THAN)
            ->createCondition('foo', 5, Filter::OPERATOR_IS_LESS_THAN)
        ;
        $this->assertTrue($this->testable->hasConditionsByField('foo'));
        $this->testable->removeConditionsByField('foo');
        $this->assertFalse($this->testable->hasConditionsByField('foo'));
    }

    public function testDeleteCondition()
    {
        $this->testable
            ->createCondition('active', 1)
            ->createCondition('id', [1, 2, 3], Filter::OPERATOR_IS_IN)
            ->deleteCondition('active', Filter::OPERATOR_IS_EQUAL)
        ;
        $this->assertFalse($this->testable->hasCondition('active', Filter::OPERATOR_IS_EQUAL));
        $this->assertTrue($this->testable->hasCondition('id', Filter::OPERATOR_IS_IN));
    }

    public function testClear()
    {
        $this->testable
            ->createCondition('active', 1)
            ->createCondition('id', [1, 2, 3], Filter::OPERATOR_IS_IN)
        ;
        $this->assertFalse($this->testable->isEmpty());
        $this->testable->clear();
        $this->assertTrue($this->testable->isEmpty());
    }

    public function testOrderBy()
    {
        $this->testable->setOrderBy(['foo' => 'asc']);
        $this->assertEquals(['foo' => 'asc'], $this->testable->getOrderBy());
        $this->testable->addOrderBy('bar', 'desc');
        $this->assertEquals(['foo' => 'asc', 'bar' => 'desc'], $this->testable->getOrderBy());
    }

    public function testGroupBy()
    {
        $this->testable->setGroupBy(['foo']);
        $this->assertEquals(['foo'], $this->testable->getGroupBy());
        $this->testable->addGroupBy('bar');
        $this->assertEquals(['foo', 'bar'], $this->testable->getGroupBy());
    }

    public function testInclude()
    {
        $this->testable->includes(['foo']);
        $this->assertEquals(['foo'], $this->testable->getIncludes());
        $this->assertTrue($this->testable->isIncluded('foo'));

        $this->testable->include('bar');
        $this->assertEquals(['foo', 'bar'], $this->testable->getIncludes());
        $this->assertTrue($this->testable->isIncluded('bar'));
    }
}
