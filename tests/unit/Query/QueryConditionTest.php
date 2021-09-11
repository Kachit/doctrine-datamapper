<?php
use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\Condition;

class QueryConditionTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCreateConditionScalar()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_EQUAL, 1);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(FilterInterface::OPERATOR_IS_EQUAL, $condition->getOperator());
        $this->assertFalse($condition->isList());
    }

    public function testCreateConditionList()
    {
        $condition = new Condition('foo', FilterInterface::OPERATOR_IS_IN, [1]);
        $this->assertEquals([1], $condition->getValue());
        $this->assertEquals('foo', $condition->getField());
        $this->assertEquals(FilterInterface::OPERATOR_IS_IN, $condition->getOperator());
        $this->assertTrue($condition->isList());
    }
}
