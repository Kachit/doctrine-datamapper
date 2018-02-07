<?php
/**
 * Class QueryCacheInterface
 *
 * @package Kachit\Database\Query
 * @author Kachit
 */
namespace Kachit\Database\Query;

interface CacheInterface
{
    /**
     * @return integer
     */
    public function getLifetime();

    /**
     * @return string
     *
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function getCacheKey();
}