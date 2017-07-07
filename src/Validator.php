<?php
/**
 * Class ValidatorInterface
 *
 * @package Kachit\Database
 * @author Kachit
 */
namespace Kachit\Database;

class Validator implements EntityValidatorInterface
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $error;

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
     * @return bool
     */
    public function isValid(EntityInterface $entity): bool
    {
        $actualClass = get_class($entity);
        if ($entity->isNull()) {
            $this->error = sprintf('Entity "%s" is null', $actualClass);
        }
        if (!$entity instanceof $this->entityClass) {
            $this->error = sprintf('Entity "%s" is not valid', $actualClass);
        }
        if (empty($entity->getPk())) {
            $this->error = sprintf('Entity "%s" has no primary key', $actualClass);
        }
        return $this->error;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}