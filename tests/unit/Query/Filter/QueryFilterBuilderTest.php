<?php
use Kachit\Database\Query\Filter\Builder;
use Kachit\Database\Query\FilterInterface;
use Kachit\Database\Query\Filter;

class QueryFilterBuilderTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Builder
     */
    protected $testable;

    protected function _before()
    {
        $this->testable = new Builder();
    }

    public function testBuildFilterByDefault()
    {
        $filter = $this->testable->getFilter();
        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertTrue($filter->isEmpty());
    }

    public function testBuildFilterRecreation()
    {
        $filter = $this->testable->eq('foo', 'bar')->getFilter();
        $newFilter = $this->testable->create()->getFilter();
        $this->assertNotEquals($filter, $newFilter);
        $this->assertInstanceOf(Filter::class, $newFilter);
        $this->assertInstanceOf(FilterInterface::class, $newFilter);
        $this->assertTrue($newFilter->isEmpty());
    }

    public function testBuildFilterRecreationFromFilter()
    {
        $filter = $this->testable->eq('foo', 'bar')->getFilter();
        $newFilter = $this->testable->create($filter)->getFilter();
        $this->assertInstanceOf(Filter::class, $newFilter);
        $this->assertInstanceOf(FilterInterface::class, $newFilter);
        $this->assertTrue($filter->hasCondition('foo', FilterInterface::OPERATOR_IS_EQUAL));
    }

    public function testEq()
    {
        $filter = $this->testable->eq('foo', 'bar')->getFilter();
        $this->assertTrue($filter->hasCondition('foo', FilterInterface::OPERATOR_IS_EQUAL));
    }

    public function testNeq()
    {
        $filter = $this->testable->neq('foo', 'bar')->getFilter();
        $this->assertTrue($filter->hasCondition('foo', FilterInterface::OPERATOR_IS_NOT_EQUAL));
    }

    public function testIn()
    {
        $filter = $this->testable->in('qwer', [1, 2, 3])->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_IN));
    }

    public function testNin()
    {
        $filter = $this->testable->nin('qwer', [1, 2, 3])->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_NOT_IN));
    }

    public function testGt()
    {
        $filter = $this->testable->gt('qwer', 1)->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_GREATER_THAN));
    }

    public function testGte()
    {
        $filter = $this->testable->gte('qwer', 1)->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_GREATER_THAN_OR_EQUAL));
    }

    public function testLt()
    {
        $filter = $this->testable->lt('qwer', 1)->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_LESS_THAN));
    }

    public function testLte()
    {
        $filter = $this->testable->lte('qwer', 1)->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_LESS_THAN_OR_EQUAL));
    }

    public function testWithNull()
    {
        $filter = $this->testable->withNull('qwer')->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_NULL));
    }

    public function testIsNull()
    {
        $filter = $this->testable->isNull('qwer')->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_NULL));
    }

    public function testWithNotNull()
    {
        $filter = $this->testable->withNotNull('qwer')->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_NOT_NULL));
    }

    public function testIsNotNull()
    {
        $filter = $this->testable->isNotNull('qwer')->getFilter();
        $this->assertTrue($filter->hasCondition('qwer', FilterInterface::OPERATOR_IS_NOT_NULL));
    }

    public function testLimit()
    {
        $filter = $this->testable->limit(10)->getFilter();
        $this->assertEquals(10, $filter->getLimit());
    }

    public function testOffset()
    {
        $filter = $this->testable->offset(10)->getFilter();
        $this->assertEquals(10, $filter->getOffset());
    }

    public function testOrderDefault()
    {
        $filter = $this->testable->order('foo')->getFilter();
        $this->assertEquals(['foo' => 'asc'], $filter->getOrderBy());
    }

    public function testOrderByDefault()
    {
        $filter = $this->testable->orderBy('foo')->getFilter();
        $this->assertEquals(['foo' => 'asc'], $filter->getOrderBy());
    }

    public function testOrderASC()
    {
        $filter = $this->testable->order('foo', true)->getFilter();
        $this->assertEquals(['foo' => 'asc'], $filter->getOrderBy());
    }

    public function testOrderDESC()
    {
        $filter = $this->testable->order('foo', false)->getFilter();
        $this->assertEquals(['foo' => 'desc'], $filter->getOrderBy());
    }

    public function testGroup()
    {
        $filter = $this->testable->group('foo')->getFilter();
        $this->assertEquals(['foo'], $filter->getGroupBy());
    }

    public function testGroupBy()
    {
        $filter = $this->testable->groupBy('foo')->getFilter();
        $this->assertEquals(['foo'], $filter->getGroupBy());
    }

    public function testInclude()
    {
        $filter = $this->testable->include('foo')->getFilter();
        $this->assertEquals(['foo'], $filter->getIncludes());
    }

    public function testIncludes()
    {
        $filter = $this->testable->includes(['foo'])->getFilter();
        $this->assertEquals(['foo'], $filter->getIncludes());
    }

    public function testFields()
    {
        $filter = $this->testable->fields(['foo'])->getFilter();
        $this->assertEquals(['foo'], $filter->getFields());
    }
}
