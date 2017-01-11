<?php
use Kachit\Database\Query\Filter;
use Kachit\Database\Query\Parser;

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
        $parser = new Parser();
        $filter = $parser->parse($query);
        $this->assertTrue($filter->hasCondition('active', '='));
        $this->assertTrue($filter->hasCondition('id', 'IN'));
        $condition = $filter->getCondition('active', '=');
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf('Kachit\Database\Query\Condition', $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('active', $condition->getField());
        $this->assertEquals('=', $condition->getOperator());
        $condition = $filter->getCondition('id', 'IN');
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf('Kachit\Database\Query\Condition', $condition);
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
        $parser = new Parser();
        $filter = $parser->parse($query);
        $this->assertTrue($filter->hasCondition('active', '='));
        $this->assertTrue($filter->hasCondition('id', 'IN'));
        $condition = $filter->getCondition('active', '=');
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf('Kachit\Database\Query\Condition', $condition);
        $this->assertEquals(1, $condition->getValue());
        $this->assertEquals('active', $condition->getField());
        $this->assertEquals('=', $condition->getOperator());
        $condition = $filter->getCondition('id', 'IN');
        $this->assertNotEmpty($condition);
        $this->assertTrue(is_object($condition));
        $this->assertInstanceOf('Kachit\Database\Query\Condition', $condition);
        $this->assertEquals([1, 2, 3], $condition->getValue());
        $this->assertEquals('id', $condition->getField());
        $this->assertEquals('IN', $condition->getOperator());
    }
}