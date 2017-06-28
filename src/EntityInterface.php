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
    public function toArray(): array;

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function fillFromArray(array $data): EntityInterface;

    /**
     * @param mixed $field
     * @return bool
     */
    public function hasEntityField($field): bool;

    /**
     * @param mixed $field
     * @return mixed
     */
    public function getEntityField($field);

    /**
     * @param mixed $field
     * @param mixed $value
     * @return EntityInterface
     */
    public function setEntityField($field, $value): EntityInterface;
}