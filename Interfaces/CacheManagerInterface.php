<?php

namespace Hr\ApiBundle\Interfaces;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Interface CacheManagerInterface
 * @package App\Interfaces
 */
interface CacheManagerInterface extends CacheItemPoolInterface
{
    /**
     * Get a cache Item Object
     * @param string $key
     * @return CacheItemInterface
     */
    public function createItem(string $key): CacheItemInterface;
}