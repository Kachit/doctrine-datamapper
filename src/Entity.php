<?php
/**
 * Abstract entity class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

use Kachit\Database\Exception\EntityException;
use JsonSerializable;

abstract class Entity implements EntityInterface, JsonSerializable
{
    /**
     * Entity constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->fillFromArray($data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function fillFromArray(array $data): EntityInterface
    {
        foreach ($this as $key => $value) {
            $this->$key = (isset($data[$key])) ? $data[$key] : $value;
        }
        return $this;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasEntityField(string $field): bool
    {
        return property_exists($this, $field);
    }

    /**
     * @param string $field
     * @return mixed
     * @throws EntityException
     */
    public function getEntityField(string $field)
    {
        if (!$this->hasEntityField($field)) {
            throw new EntityException('Property is not exists');
        }
        return $this->$field;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return EntityInterface
     * @throws EntityException
     */
    public function setEntityField(string $field, $value): EntityInterface
    {
        if (!$this->hasEntityField($field)) {
            throw new EntityException('Property is not exists');
        }
        $this->$field = $value;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return false;
    }
}