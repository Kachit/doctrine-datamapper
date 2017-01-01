<?php
/**
 * Nullable interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

interface NullableInterface
{
    /**
     * @return bool
     */
    public function isNull();
}