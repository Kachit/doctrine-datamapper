<?php
/**
 * Null entity class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

class NullEntity implements EntityInterface
{
    public function __construct(array $data = [])
    {
        $this->fillFromArray($data);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function fillFromArray(array $data): EntityInterface
    {
        return $this;
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getEntityField(string $field)
    {
        return null;
    }

    /**
     * @param mixed $field
     * @return bool
     */
    public function hasEntityField(string $field): bool
    {
        return false;
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @return EntityInterface
     */
    public function setEntityField(string $field, $value): EntityInterface
    {
        return $this;
    }

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @param $name string
     * @param $arguments array
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    public function __call($name, $arguments)
    {
        return $this->__callHandler($name, $arguments);
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return true;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return null|EntityInterface
     */
    protected function __callHandler($name, $arguments = [])
    {
        $result = null;
        switch(true) {
            case (0 === strpos($name, 'get')):
                $result = null;
                break;
            case (0 === strpos($name, 'set')):
                $result = $this;
                break;
            default:
                $result = null;
        }
        return $result;
    }
}
