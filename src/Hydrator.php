<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 17.12.2016
 * Time: 19:36
 */
namespace Kachit\Silex\Database;

class Hydrator implements HydratorInterface
{
    /**
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface|NullEntity
     */
    public function hydrate(array $data, EntityInterface $entity)
    {
        $data = $this->convertForHydrate($data);
        return ($data) ? $entity->fillFromArray($data) : new NullEntity();
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