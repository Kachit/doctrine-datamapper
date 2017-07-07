<?php
/**
 * Class Connection
 *
 * @author Kachit
 */
namespace Mocks;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Mockery;
use Mockery\MockInterface;

class DBALConnectionMock
{
    /**
     * @var DBALConnection|MockInterface
     */
    private $mock;

    /**
     * @return $this
     */
    public function createObject()
    {
        $this->mock = Mockery::mock(DBALConnection::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
        ;
        return $this;
    }

    /**
     * @return $this
     */
    public function withExpressionBuilder()
    {
        $this->mock->shouldReceive('getExpressionBuilder')->andReturn(new ExpressionBuilder($this->mock));
        return $this;
    }

    /**
     * @return DBALConnection|MockInterface
     */
    public function get()
    {
        return $this->mock;
    }
}
