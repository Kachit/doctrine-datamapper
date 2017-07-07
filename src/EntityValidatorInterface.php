<?php
/**
 * Class EntityValidatorInterface
 *
 * @package Kachit\Database
 * @author Kachit
 */
namespace Kachit\Database;

interface EntityValidatorInterface
{
    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function isValid(EntityInterface $entity): bool;

    /**
     * @return string
     */
    public function getError(): string;
}