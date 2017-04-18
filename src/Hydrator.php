<?php
/**
 * Hydrator class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

class Hydrator implements HydratorInterface
{
    /**
     * @var EntityInterface
     */
    protected $nullEntity;

    /**
     * Hydrator constructor.
     * @param EntityInterface|null $nullEntity
     */
    public function __construct(EntityInterface $nullEntity = null)
    {
        $this->nullEntity = ($nullEntity) ? $nullEntity : new NullEntity();
    }

    /**
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface|NullEntity
     */
    public function hydrate(array $data, EntityInterface $entity)
    {
        return ($data) ? $entity->fillFromArray($this->convertForHydrate($data)) : $this->createNullEntity();
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function extract(EntityInterface $entity)
    {
        return $this->convertForExtract($entity->toArray());
    }

    /**
     * @param array $data
     * @return array
     */
    protected function convertForHydrate(array $data)
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function convertForExtract(array $data)
    {
        return $data;
    }

    /**
     * @return EntityInterface|NullEntity
     */
    protected function createNullEntity()
    {
        return clone $this->nullEntity;
    }
}