<?php
namespace Kachit\Database\Gateway\Traits;

use Kachit\Database\GatewayInterface;
/**
 * Insert raw plugin
 *
 * @author Kachit
 * @package Kachit\Database
 * @property GatewayInterface $this
 */
trait InsertRawTrait
{
    /**
     * @param array $data
     * @return int
     */
    public function insert(array $data): int
    {
        return $this->getConnection()->insert($this->getTableName(), $data);
    }
}
