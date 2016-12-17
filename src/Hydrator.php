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
     * @var string
     */
    private $entityClass;

    /**
     * Hydrator constructor.
     * @param string $entityClass
     */
    public function __construct($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function hydrate(array $data)
    {
        $data = $this->convertForHydrate($data);
        $entity = $this->createEntityObject();
        return $entity->fillFromArray($data);
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
     * @return EntityInterface
     */
    protected function createEntityObject()
    {
        $entityClass = $this->entityClass;
        return new $entityClass();
    }
}