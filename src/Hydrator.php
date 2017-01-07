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
        $data = $this->convertForHydrate($data);
        return ($data) ? $entity->fillFromArray($data) : $this->nullEntity;
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
}