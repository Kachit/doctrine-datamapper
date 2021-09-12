<?php
/**
 * Class Gateway
 *
 * @package Stubs\DB
 * @author Kachit
 */
namespace Stubs\DB;

use Kachit\Database\Gateway\Traits\SoftDeleteTrait;

class GatewaySoftDelete extends Gateway
{
    use SoftDeleteTrait;

    /**
     * @return array
     */
    protected function getSoftDeleteCondition(): array
    {
        return ['active' => 'false'];
    }
}
