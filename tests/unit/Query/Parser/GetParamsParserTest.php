<?php
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Parser\GetParamsQuery;
use Kachit\Database\Query\Condition;

class GetParamsParserTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testCreateFilterFromArray()
    {
        $query = ['filter' =>
            [
                'active' => 1,
                'id' => [
                    '$in' => '1, 2, 3'
                ]
            ]
        ];
        $parser = new GetParamsQuery();
        $filter = $parser->parse($query);
        $this->assertInstanceOf(Filter::class, $filter);
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
    public function testCreateFilterWithOrderBy()
    {
        $query = ['order' => [
            'foo' => 'asc',
        ]];
        $parser = new GetParamsQuery();
        $filter = $parser->parse($query);
        $this->assertInstanceOf(Filter::class, $filter);
        $order = $filter->getOrderBy();
        $this->assertArrayHasKey('foo', $order);
        $this->assertEquals('asc', $order['foo']);
    }

    /**
     *
     */
    public function testCreateFilterWithLimitOffset()
    {
        $query = ['limit' => 10, 'offset' => 10];
        $parser = new GetParamsQuery();
        $filter = $parser->parse($query);
        $this->assertEquals($query['limit'], $filter->getLimit());
        $this->assertEquals($query['offset'], $filter->getOffset());
    }

    /**
     *
     */
    public function testCreateFilterFromEmpty()
    {
        $parser = new GetParamsQuery();
        $filter = $parser->parse(null);
        $this->assertInstanceOf(Filter::class, $filter);
    }

    /**
     *
     */
    public function testParseSingleIncludes()
    {
        $query = ['include' => 'author',];
        $parser = new GetParamsQuery();
        $filter = $parser->parse($query);
        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertTrue($filter->isIncluded('author'));
    }

    /**
     *
     */
    public function testParseMultipleIncludes()
    {
        $query = ['include' => 'user,photo',];
        $parser = new GetParamsQuery();
        $filter = $parser->parse($query);
        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertTrue($filter->isIncluded('user'));
        $this->assertTrue($filter->isIncluded('photo'));
    }
}