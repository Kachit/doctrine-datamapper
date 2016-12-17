<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 26.10.2016
 * Time: 20:30
 */
namespace Kachit\Silex\Database;

use Traversable;

class Collection implements CollectionInterface, \JsonSerializable, \IteratorAggregate
{
    /**
     * @var EntityInterface[]
     */
    protected $data = [];

    /**
     * @param EntityInterface $entity
     * @return $this
     */
    public function add(EntityInterface $entity)
    {
        $this->data[] = $entity;
        return $this;
    }

    /**
     * @param mixed $index
     * @return EntityInterface
     */
    public function get($index)
    {
        return $this->data[$index];
    }

    /**
     * @param mixed $index
     * @return bool
     */
    public function has($index)
    {
        return isset($this->data[$index]);
    }

    /**
     * @return EntityInterface[]
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArrayRecursive()
    {
        return array_map(function(EntityInterface $entity){
            return $entity->toArray();
        }, $this->toArray());
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
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
}