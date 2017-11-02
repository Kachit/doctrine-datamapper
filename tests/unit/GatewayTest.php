<?php
use Stubs\DB\Gateway;

class GatewayTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Gateway|PHPUnit_Framework_MockObject_MockObject
     */
    protected $testable;

    /**
     *
     */
    protected function _before()
    {
        $this->testable = $this->getMockBuilder(Gateway::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->testable->method('getTableName')->willReturn('users');
    }

    /**
     *
     */
    public function testGetTableName()
    {
        $result = $this->testable->getTableName();
        $this->assertEquals('users', $result);
    }
}