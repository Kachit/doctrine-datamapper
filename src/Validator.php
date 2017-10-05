<?php
/**
 * Class ValidatorInterface
 *
 * @package Kachit\Database
 * @author Kachit
 */
namespace Kachit\Database;

use Kachit\Database\Exception\EntityException;

class Validator implements EntityValidatorInterface
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * Validator constructor
     *
     * @param string $entityClass
     */
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @param EntityInterface $entity
     * @throws EntityException
     */
    public function validate(EntityInterface $entity)
    {
        $actualClass = get_class($entity);
        if ($entity->isNull()) {
            throw new EntityException(sprintf('Entity "%s" is null', $actualClass));
        }
        if (!$entity instanceof $this->entityClass) {
            throw new EntityException(sprintf('Entity "%s" is not valid', $actualClass));
        }
    }
}