<?php
/**
 * Hydrator interface
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database;

interface HydratorInterface
{
    /**
     * @param array $data
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function hydrate(array $data, EntityInterface $entity);

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function extract(EntityInterface $entity);
}