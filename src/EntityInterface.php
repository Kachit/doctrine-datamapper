<?php
/**
 * Entity interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

interface EntityInterface extends NullableInterface
{
    /**
     * @return mixed
     */
    public function getPk();

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function setPk($pk);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function fillFromArray(array $data);
}