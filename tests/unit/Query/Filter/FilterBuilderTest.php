<?php
use Kachit\Database\Query\Filter\Builder;
use Kachit\Database\Query\Filter;

class FilterBuilderTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Builder
     */
    protected $testable;

    /**
     *
     */
    protected function _before()
    {
        $this->testable = new Builder();
    }

    /**
     *
     */
    public function testBuildSimpleFilter()
    {
        $filter = $this->testable->eq('foo', 'bar')->in('qwer', [1, 2, 3])->limit(10)->getFilter();
        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertTrue($filter->hasCondition('foo', '='));
        $this->assertTrue($filter->hasCondition('qwer', 'IN'));
        $this->assertEquals(10, $filter->getLimit());
    }

    /**
     *
     */
    public function testBuildFilterRecreation()
    {
        $filter = $this->testable->eq('foo', 'bar')->in('qwer', [1, 2, 3])->limit(10)->getFilter();
        $newFilter = $this->testable->create()->getFilter();
        $this->assertNotEquals($filter, $newFilter);
    }
}