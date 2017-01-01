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
    /**
     * @return mixed
     */
    public function getPk()
    {
        return null;
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