<?php
/**
 * Class Configuration
 *
 * @package Kachit\Database\Gateway\Traits
 * @author Kachit
 */
namespace Kachit\Database\Gateway;

class Configuration
{
    /**
     * @var int
     */
    protected $cacheLifeTime = 0;

    /**
     * @var null
     */
    protected $cacheKey = null;

    /**
     * @return int
     */
    public function getCacheLifeTime(): int
    {
        return $this->cacheLifeTime;
    }

    /**
     * @param int $cacheLifeTime
     * @return Configuration
     */
    public function setCacheLifeTime(int $cacheLifeTime): Configuration
    {
        $this->cacheLifeTime = $cacheLifeTime;
        return $this;
    }

    /**
     * @return null
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * @param null $cacheKey
     * @return Configuration
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }
}