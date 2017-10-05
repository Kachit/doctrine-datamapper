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
     * @param string|null $primaryKeyColumn
     * @return mixed
     * @throws EntityException
     */
    public function validate(EntityInterface $entity, string $primaryKeyColumn = null);
}