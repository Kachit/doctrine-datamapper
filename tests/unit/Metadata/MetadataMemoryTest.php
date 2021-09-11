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

    public function testFilterRow()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => true];
        $result = $this->testable->filterRow($row);
        $this->assertEquals($row, $result);
    }

    public function testFilterRowWithFalseValue()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => false];
        $expected = ['id' => 1, 'foo' => 'foo', 'bar' => 'false'];
        $result = $this->testable->filterRow($row);
        $this->assertEquals($expected, $result);
    }

    public function testFilterRowWithFiltered()
    {
        $row = ['id' => 1, 'foo' => 'foo', 'bar' => true, 'qwer' => 'zqwer'];
        $expected = ['id' => 1, 'foo' => 'foo', 'bar' => true];
        $result = $this->testable->filterRow($row);
        $this->assertEquals($expected, $result);
    }
}
