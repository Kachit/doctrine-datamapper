<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 17.12.2016
 * Time: 19:37
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