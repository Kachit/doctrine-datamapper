<?php
/**
 * Class GenericEntity
 *
 * @package Orbitum\Billing\Models\Entities\Virtual
 * @author Kachit
 */
namespace Kachit\Database;

class GenericEntity extends Entity
{
    /**
     * @var array
     */
    private $properties = [];

    /**
     * @return mixed
     */
    public function getPk()
    {
        return microtime();
    }

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function setPk($pk)
    {
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->properties[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function fillFromArray(array $data): EntityInterface
    {
        $this->properties = $data;
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
     * @param string $name
     * @param array $arguments
     * @return null|EntityInterface
     */
    protected function __callHandler($name, $arguments = [])
    {
        $count = 1;
        $result = null;
        switch(true) {
            case (0 === strpos($name, 'get')):
                $property = strtolower(str_replace('get', '', $name, $count));
                $result = $this->properties[$property];
                break;
            case (0 === strpos($name, 'set')):
                $property = strtolower(str_replace('set', '', $name, $count));
                $this->properties[$property] = $arguments[0];
                $result = $this;
                break;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->properties;
    }
}