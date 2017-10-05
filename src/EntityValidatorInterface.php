<?php
/**
 * Class EntityValidatorInterface
 *
 * @package Kachit\Database
 * @author Kachit
 */
namespace Kachit\Database;

use Kachit\Database\Exception\EntityException;

interface EntityValidatorInterface
{
    /**
     * @param EntityInterface $entity
     * @throws EntityException
     */
    public function validate(EntityInterface $entity);
}