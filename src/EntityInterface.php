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
     * @return array
     */
    public function toArray(): array;

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function fillFromArray(array $data): EntityInterface;

    /**
     * @param string $field
     * @return bool
     */
    public function hasEntityField(string $field): bool;

    /**
     * @param string $field
     * @return mixed
     */
    public function getEntityField(string $field);

    /**
     * @param string $field
     * @param mixed $value
     * @return EntityInterface
     */
    public function setEntityField(string $field, $value): EntityInterface;
}