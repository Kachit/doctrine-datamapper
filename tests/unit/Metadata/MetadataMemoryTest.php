<?php
use Kachit\Database\MetaData\Memory as MetaDataMemory;

class MetadataMemoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var MetaDataMemory
     */
    protected $testable;

    protected function _before()
    {
        $this->testable = new MetaDataMemory('foo', 'id', ['id', 'foo', 'bar']);
    }

    public function testGetPrimaryKeyColumn()
    {
        $this->assertEquals('id', $this->testable->getPrimaryKeyColumn());
    }

    public function testGetColumns()
    {
        $this->assertEquals(['id', 'foo', 'bar'], $this->testable->getColumns());
    }

    public function testConvertValue()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'convertValue', ['foo']);
        $this->assertEquals('foo', $result);
    }

    public function testConvertFalse()
    {
        $result = $this->tester->callNonPublicMethod($this->testable, 'convertValue', [false]);
        $this->assertEquals('false', $result);
    }

    public function testFilterRowForInsert()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => true];
        $result = $this->testable->filterRowForInsert($row);
        $this->assertEquals($row, $result);
    }

    public function testFilterRowForInsertWithFalseValue()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => false];
        $expected = ['id' => 1, 'foo' => 'foo', 'bar' => 'false'];
        $result = $this->testable->filterRowForInsert($row);
        $this->assertEquals($expected, $result);
    }

    public function testFilterRowForInsertWithNullValue()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => null];
        $expected = ['id' => 1, 'foo' => 'foo'];
        $result = $this->testable->filterRowForInsert($row);
        $this->assertEquals($expected, $result);
    }

    public function testFilterRowForInsertWithFiltered()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => true, 'qwer' => 'zqwer'];
        $expected = ['id' => 1, 'foo' => 'foo', 'bar' => true];
        $result = $this->testable->filterRowForInsert($row);
        $this->assertEquals($expected, $result);
    }

    public function testFilterRowForUpdate()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => true];
        $expected = ['foo' => 'foo', 'bar' => true];
        $result = $this->testable->filterRowForUpdate($row);
        $this->assertEquals($expected, $result);
    }

    public function testFilterRowForUpdateNullValues()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => null];
        $expected = ['foo' => 'foo', 'bar' => null];
        $result = $this->testable->filterRowForUpdate($row);
        $this->assertEquals($expected, $result);
    }

    public function testFilterRowForUpdateWithFiltered()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => null, 'r' => 'r'];
        $expected = ['foo' => 'foo', 'bar' => null];
        $result = $this->testable->filterRowForUpdate($row);
        $this->assertEquals($expected, $result);
    }
}
