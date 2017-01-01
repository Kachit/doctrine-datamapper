<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 17.12.2016
 * Time: 20:15
 */
namespace Kachit\Database;

interface NullableInterface
{
    /**
     * @return bool
     */
    public function isNull();
}