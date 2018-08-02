<?php

namespace Hr\ApiBundle\Interfaces;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;

/**
 * Interface CacheItemInterface
 * @package App\Interfaces
 */
interface CacheItemInterface extends PsrCacheItemInterface
{
    /**
     * Get the time to live of an item
     * @return int|null
     */
    public function getTtl(): ?int;
}