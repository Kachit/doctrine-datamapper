<?php
/**
 * Class Entity
 * @package Stubs\DB
 * @author Kachit
 */
namespace Stubs\DB;

use Kachit\Database\Entity as AbstractEntity;
use Kachit\Database\EntityInterface;

class Entity extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $active;

    /**
     * @return mixed
     */
    public function getPk()
    {
        return $this->getId();
    }

    /**
     * @param mixed $pk
     * @return EntityInterface
     */
    public function setPk($pk)
    {
        return $this->setId($pk);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Entity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Entity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Entity
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     * @return Entity
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }
}
