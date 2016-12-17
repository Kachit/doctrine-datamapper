<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 26.10.2016
 * Time: 20:30
 */
namespace Kachit\Silex\Database;

abstract class Entity implements EntityInterface, \JsonSerializable
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
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fillFromArray(array $data)
    {
        foreach ($this as $key => $value) {
            $this->$key = (isset($data[$key])) ? $data[$key] : $value;
        }
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
    public function isNull()
    {
        return false;
    }
}