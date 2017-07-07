<?php
/**
 * Query condition class
 *
 * @author Kachit
 * @package Kachit\Database
 */
namespace Kachit\Database\Query;

class Condition
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Condition constructor
     *
     * @param $field
     * @param $operator
     * @param $value
     */
    public function __construct(string $field, string $operator, $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     * @return $this
     */
    public function setOperator(string $operator)
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return is_array($this->value);
    }
}