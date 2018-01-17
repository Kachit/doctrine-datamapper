<?php
/**
 * Class FilterInterface
 *
 * @package Kachit\Database\Query
 * @author Kachit
 */
namespace Kachit\Database\Query;

interface FilterInterface
{
    const OPERATOR_IS_EQUAL = '=';
    const OPERATOR_IS_NOT_EQUAL = '!=';
    const OPERATOR_IS_GREATER_THAN = '>';
    const OPERATOR_IS_GREATER_THAN_OR_EQUAL = '>=';
    const OPERATOR_IS_IN = 'IN';
    const OPERATOR_IS_NOT_IN = 'NOT IN';
    const OPERATOR_IS_LESS_THAN = '<';
    const OPERATOR_IS_LESS_THAN_OR_EQUAL = '<=';
    const OPERATOR_IS_LIKE = 'LIKE';
    const OPERATOR_IS_NULL = 'NULL';
    const OPERATOR_IS_NOT_NULL = 'NOT NULL';

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @return array
     */
    public function getConditions(): array;

    /**
     * @param string $field
     * @param string $operator
     * @return bool
     */
    public function hasCondition(string $field, string $operator = self::OPERATOR_IS_EQUAL): bool;

    /**
     * @param string $field
     * @param string $operator
     * @return Condition
     */
    public function getCondition(string $field, string $operator = self::OPERATOR_IS_EQUAL): Condition;

    /**
     * @param string $field
     * @param string $operator
     * @return FilterInterface
     */
    public function deleteCondition(string $field, string $operator = self::OPERATOR_IS_EQUAL): FilterInterface;

    /**
     * @param Condition $condition
     * @return FilterInterface
     */
    public function addCondition(Condition $condition): FilterInterface;

    /**
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return FilterInterface
     */
    public function createCondition(string $field, $value, string $operator = self::OPERATOR_IS_EQUAL): FilterInterface;

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @param int $limit
     * @return FilterInterface
     */
    public function setLimit(int $limit): FilterInterface;

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * @param int $offset
     * @return FilterInterface
     */
    public function setOffset(int $offset): FilterInterface;

    /**
     * @return array
     */
    public function getOrderBy():  array;

    /**
     * @param array $orderBy
     * @return FilterInterface
     */
    public function setOrderBy(array $orderBy): FilterInterface;

    /**
     * @param string $field
     * @param string $order
     * @return FilterInterface
     */
    public function addOrderBy(string $field, string $order): FilterInterface;

    /**
     * @return array
     */
    public function getGroupBy(): array;

    /**
     * @param array $groupBy
     * @return FilterInterface
     */
    public function setGroupBy(array $groupBy): FilterInterface;

    /**
     * @param string $field
     * @return FilterInterface
     */
    public function addGroupBy(string $field): FilterInterface;

    /**
     * @return FilterInterface
     */
    public function clear(): FilterInterface;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param string $value
     * @return FilterInterface
     */
    public function include(string $value): FilterInterface;

    /**
     * @param array $values
     * @return FilterInterface
     */
    public function includes(array $values): FilterInterface;

    /**
     * @param string $value
     * @return bool
     */
    public function isIncluded(string $value): bool;
}