<?php
use Kachit\Database\Query\Builder;
use Kachit\Database\Query\Filter\Builder as FilterBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Mocks\DBALConnectionMock;

class QueryBuilderTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Builder
     */
    protected $testable;

    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @var FilterBuilder
     */
    protected $fb;

    protected function _before()
    {
        $this->testable = new Builder('t');
        $this->fb = new FilterBuilder();
        $this->qb = new QueryBuilder((new DBALConnectionMock())->createObject()->withExpressionBuilder()->get());
    }

    public function testBuilderEmpty()
    {
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $this->testable->build($queryBuilder);
        $this->assertEquals('SELECT * FROM table t', $queryBuilder->getSQL());
        $this->assertEmpty($queryBuilder->getParameters());
    }

    protected function testBuilderLimitOffset()
    {
        $filter = $this->fb->limit(10)->offset(10)->getFilter();
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t', $queryBuilder->getSQL());
        $this->assertEmpty($queryBuilder->getParameters());
    }

    public function testBuilderOrderBy()
    {
        $filter = $this->fb->orderBy('foo')->getFilter();
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t ORDER BY t.foo asc', $queryBuilder->getSQL());
        $this->assertEmpty($queryBuilder->getParameters());
    }

    public function testBuilderOrderByMultiple()
    {
        $filter = $this->fb->orderBy('foo')->orderBy('bar')->getFilter();
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t ORDER BY t.foo asc, t.bar asc', $queryBuilder->getSQL());
        $this->assertEmpty($queryBuilder->getParameters());
    }

    public function testBuilderGroupBy()
    {
        $filter = $this->fb->groupBy('foo')->getFilter();
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t GROUP BY t.foo', $queryBuilder->getSQL());
        $this->assertEmpty($queryBuilder->getParameters());
    }

    public function testBuilderGroupByMultiple()
    {
        $filter = $this->fb->groupBy('foo')->groupBy('bar')->getFilter();
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t GROUP BY t.foo, t.bar', $queryBuilder->getSQL());
        $this->assertEmpty($queryBuilder->getParameters());
    }

    public function testBuildSimpleSelect()
    {
        $expected = [
            'dcValue1' => 'zwer',
            'dcValue2' => [1, 2, 3]
        ];
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $filter = $this->fb->eq('cwer', 'zwer')->in('foo', [1, 2, 3])->getFilter();
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t WHERE (t.cwer = :dcValue1) AND (t.foo IN (:dcValue2))', $queryBuilder->getSQL());
        $this->assertEquals($expected, $queryBuilder->getParameters());
    }

    public function testBuildSimpleSelectWithEmptyFilter()
    {
        $expected = [];
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $filter = $this->fb->getFilter();
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t', $queryBuilder->getSQL());
        $this->assertEquals($expected, $queryBuilder->getParameters());
    }

    public function testBuildQueryWithSelectedFields()
    {
        $expected = [];
        $queryBuilder = $this->qb->select()->from('table', 't');
        $filter = $this->fb->getFilter()->setFields(['id', 'name']);
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT t.id, t.name FROM table t', $queryBuilder->getSQL());
        $this->assertEquals($expected, $queryBuilder->getParameters());
    }

    public function testBuildQueryWithBooleanCondition()
    {
        $expected = ['dcValue1' => true];
        $queryBuilder = $this->qb->select('*')->from('table', 't');
        $filter = $this->fb->eq('active', true)->getFilter();
        $this->testable->build($queryBuilder, $filter);
        $this->assertEquals('SELECT * FROM table t WHERE t.active = :dcValue1', $queryBuilder->getSQL());
        $this->assertEquals($expected, $queryBuilder->getParameters());
    }
}
