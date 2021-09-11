<?php
/**
 * Class ParserInterface
 *
 * @package Kachit\Database\Query
 * @author Kachit
 */
namespace Kachit\Database\Query;

interface ParserInterface
{
    /**
     * @param mixed $query
     * @param FilterInterface $filter = null
     * @return FilterInterface
     */
    public function parse($query, FilterInterface $filter = null): FilterInterface;
}
