<?php
/**
 * Class StatementMock
 *
 * @package Kachit\Database\Mocks\Doctrine\DBAL
 * @author Kachit
 */
namespace Kachit\Database\Mocks\Doctrine\DBAL;

use Doctrine\DBAL\Statement;

class StatementMock extends Statement
{
    /**
     * Creates a new <tt>Statement</tt> for the given SQL and <tt>Connection</tt>.
     *
     * @param string                    $sql  The SQL of the statement.
     * @param \Doctrine\DBAL\Connection $conn The connection on which the statement should be executed.
     */
    public function __construct()
    {

    }
}