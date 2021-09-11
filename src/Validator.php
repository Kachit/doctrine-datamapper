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
     * @param EntityInterface $entityClass
     */
    public function __construct(EntityInterface $entityClass)
    {
        $this->entityClass = get_class($entityClass);
    }

    /**
     * @param EntityInterface $entity
     * @param string|null $pkField
     * @throws EntityException
     */
    public function validate(EntityInterface $entity, string $pkField = null)
    {
        $actualClass = get_class($entity);
        if ($entity->isNull()) {
            throw new EntityException(sprintf('Entity "%s" is null', $actualClass));
        }
        if (!$entity instanceof $this->entityClass) {
            throw new EntityException(sprintf('Entity "%s" is not valid', $actualClass));
        }
        if (!$entity->hasEntityField((string)$pkField)) {
            throw new EntityException(sprintf('Entity "%s" has not primary key field', $actualClass));
        }
    }
}
