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
     * @return EntityInterface
     */
    public function hydrate(array $data);

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function extract(EntityInterface $entity);
}