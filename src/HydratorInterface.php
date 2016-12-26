<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 17.12.2016
 * Time: 19:36
 */
namespace Kachit\Silex\Database;

interface HydratorInterface
{
    /**
     * @param array $data
     * @param EntityInterface $entity
     * @return mixed
     */
    public function hydrate(array $data, EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function extract(EntityInterface $entity);
}