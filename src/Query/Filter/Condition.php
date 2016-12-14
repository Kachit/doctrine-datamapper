<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 29.07.2016
 * Time: 0:18
 */
namespace Kachit\Silex\Database\Query\Filter;

use Doctrine\DBAL\Connection;

class Condition
{
    /**
     * @var
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
     * @var string
     */
    private $namedParam;

    /**
     * Condition constructor
     *
     * @param $field
     * @param $operator
     * @param $value
     */
    public function __construct($field, $operator, $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
        $this->namedParam = $this->field . '_' . uniqid();
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return ($this->getOperator() == Parser::OPERATOR_IS_LIKE) ? '%' . $this->value . '%' : $this->value;
    }

    /**
     * @return int|null
     */
    public function getType()
    {
        $operators = [Parser::OPERATOR_IS_IN, Parser::OPERATOR_IS_NOT_IN];
        return (in_array($this->operator, $operators)) ? Connection::PARAM_STR_ARRAY : null;
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
     * @return string
     */
    public function getConditionString()
    {
        $operators = [Parser::OPERATOR_IS_IN, Parser::OPERATOR_IS_NOT_IN];
        $namedParam = (in_array($this->operator, $operators)) ? '(:' . $this->namedParam . ')' : ':' . $this->namedParam;
        return "{$this->getField()} {$this->getOperator()} {$namedParam}";
    }

    /**
     * @return string
     */
    public function getNamedParam()
    {
        return $this->namedParam;
    }
}