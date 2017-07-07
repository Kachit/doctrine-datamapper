<?php
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Parser\JsonQuery;
use Kachit\Database\Query\Condition;

class ParserTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     *
     */
    public function testCreateFilterFromArray()
    {
        $query = ['$filter' =>
            [
                'active' => 1,
                'id' => [
                    '$in' => [1, 2, 3]
                ]
            ]
        ];
        $parser = new JsonQuery();
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
    public function testCreateFilterFromJson()
    {
        $query = ['$filter' =>
            [
                'active' => 1,
                'id' => [
                    '$in' => [1, 2, 3]
                ]
            ]
        ];
        $query = json_encode($query);
        $parser = new JsonQuery();
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
    public function testCreateFilterFromEmpty()
    {
        $parser = new JsonQuery();
        $filter = $parser->parse(null);
        $this->assertInstanceOf(Filter::class, $filter);
    }
}