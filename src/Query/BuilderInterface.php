<?php
/**
 * Class BuilderInterface
 *
 * @package Kachit\Database\Query
 * @author Kachit
 */
namespace Kachit\Database\Query;

use Doctrine\DBAL\Query\QueryBuilder;

interface BuilderInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param FilterInterface |null $filter
     */
    public function build(QueryBuilder $queryBuilder, FilterInterface $filter = null);
}