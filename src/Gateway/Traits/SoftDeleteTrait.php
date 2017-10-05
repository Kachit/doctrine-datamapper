<?php
namespace Kachit\Database\Gateway\Traits;

use Kachit\Database\Query\Filter;
use Kachit\Database\GatewayInterface;
/**
 * Soft delete plugin
 *
 * @author Kachit
 * @package Kachit\Database
 * @property GatewayInterface $this
 */
trait SoftDeleteTrait
{
    /**
     * @param Filter $filter
     * @return int
     */
    public function delete(Filter $filter = null): int
    {
        $data = $this->getSoftDeleteCondition();
        return $this->update($data, $filter);
    }

    /**
     * @return array
     */
    abstract protected function getSoftDeleteCondition(): array;
}