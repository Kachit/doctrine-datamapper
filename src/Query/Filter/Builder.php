<?php
/**
 * Created by PhpStorm.
 * User: Kachit
 * Date: 02.08.2016
 * Time: 11:26
 */
namespace Kachit\Silex\Database\Query\Filter;

class Builder
{
    /**
     * @var array
     */
    private $query = [];

    /**
     * @var Parser
     */
    private $queryParser;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->query = $this->getDefault();
        $this->queryParser = new Parser();
    }

    /**
     * @param $field
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function addCondition($field, $value, $operator = Parser::PARAM_IS_EQUAL)
    {
        $this->query[Parser::QUERY_PARAM_FILTER][$field] = [$operator => $value];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return Builder
     */
    public function addSearch($field, $value)
    {
        return $this->addCondition($field, $value, Parser::PARAM_IS_LIKE);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->query = $this->getDefault();
        return $this;
    }

    /**
     * @return Filter|null
     */
    public function build()
    {
        return $this->queryParser->parse($this->query);
    }

    /**
     * @return array
     */
    public function getDefault()
    {
        return [
            Parser::QUERY_PARAM_FILTER => [],
            Parser::QUERY_PARAM_ORDER_BY => [],
            Parser::QUERY_PARAM_LIMIT => 0,
            Parser::QUERY_PARAM_OFFSET => 0,
        ];
    }
}