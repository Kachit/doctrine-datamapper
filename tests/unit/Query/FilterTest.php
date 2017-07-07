<?php
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Condition;

class FilterTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testCreateCondition()
    {
        $filter = new Filter();
        $filter
            ->createCondition('active', 1)
            ->createCondition('id', [1, 2, 3], 'IN')
        ;
        $this->assertTrue($filter->hasCondition('active', '='));
        $this->assertTrue($filter->hasCondition('id', 'IN'));
        $condition = $filter->getCondition('active', '=');
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('active', $condition->getField());
        $this->assertEquals('=', $condition->getOperator());
        $condition = $filter->getCondition('id', 'IN');
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals([1, 2, 3], $condition->getValue());
        $this->assertEquals('id', $condition->getField());
        $this->assertEquals('IN', $condition->getOperator());
    }

    /**
     *
     */
    public function testDeleteCondition()
    {
        $filter = new Filter();
        $filter
            ->createCondition('active', 1)
            ->createCondition('id', [1, 2, 3], 'IN')
            ->deleteCondition('active', '=')
        ;
        $this->assertFalse($filter->hasCondition('active', '='));
        $this->assertTrue($filter->hasCondition('id', 'IN'));
    }
}