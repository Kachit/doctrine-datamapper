<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 26.10.2016
 * Time: 20:30
 */
namespace Kachit\Silex\Database;

class NullEntity implements EntityInterface
{
    /**
     * Entity constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {

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
        return $this;
    }

    /**
     * @return bool
     */
    public function isNull()
    {
        return true;
    }
}