<?php

namespace Hr\ApiBundle\Service\CacheManager;

use App\Interfaces\CacheItemInterface;
use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;
use App\Interfaces\CacheManagerInterface;
use Redis;

/**
 * Class RedisCache
 * @package App\Service\CacheManager
 */
class RedisCache implements CacheManagerInterface
{
    /** @var string */
    protected $host;
    /** @var string */
    protected $port;
    /** @var string */
    protected $password;
    /** @var Redis */
    protected $redisCache;

    public function __construct()
    {
        $this->redisCache = new Redis();
        $this->redisCache->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
        $this->redisCache->auth(getenv('REDIS_PASSWORD'));
    }

    /**
     * Get a new redis cache item object
     * @param string $key
     * @return CacheItemInterface
     */
    public function createItem(string $key): CacheItemInterface
    {
        return new RedisCacheItem($key);
    }

    /**
     * Check the validity of a key
     * @param string $key
     * @throws \InvalidArgumentException invalid key
     * @return bool
     */
    protected function checkIsLegalKey(string $key): bool
    {
        if (preg_match("/^[a-zA-Z0-9\:\-\.]{5,1024}$/", $key) === false) {
            throw new \InvalidArgumentException('Invalid cache key: ' . $key);
        }
        return true;
    }

    /**
     * Returns a Cache Item representing the specified key.
     * @param string $key The key for which to return the corresponding Cache Item.
     * @throws \InvalidArgumentException invalid key
     * @return CacheItemInterface The corresponding Cache Item.
     */
    public function getItem($key): CacheItemInterface
    {
        $this->checkIsLegalKey($key);

        $redisCacheItem = new RedisCacheItem($key);
        $cacheValue     = $this->redisCache->get($key);

        if (!$cacheValue) {
            $redisCacheItem->setIsHit(false);
        } else {
            $redisCacheItem->setIsHit(true);
            $redisCacheItem->set($cacheValue);
        }

        return $redisCacheItem;
    }

    /**
     * Returns a traversable set of cache items.
     * @param string[] $keys An indexed array of keys of items to retrieve.
     * @throws \InvalidArgumentException invalid key
     * @return array|\Traversable array or Traversable of CacheItemInterface object
     */
    public function getItems(array $keys = array())
    {
        $items = [];
        foreach ($keys as $key) {
            $this->checkIsLegalKey($key);
            $items[$key] = $this->getItem($key);
        }
        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     * @param string $key The key for which to check existence.
     * @throws \InvalidArgumentException invalid key
     * @return bool True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        $this->checkIsLegalKey($key);
        return $this->redisCache->exists($key);
    }

    /**
     * Deletes all items in the pool.
     * @return bool True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        return $this->redisCache->flushAll();
    }

    /**
     * Removes the item from the pool.
     * @param string $key The key to delete.
     * @throws \InvalidArgumentException invalid key
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        $this->checkIsLegalKey($key);
        return $this->redisCache->delete($key);
    }

    /**
     * Removes multiple items from the pool.
     * @param string[] $keys An array of keys that should be removed from the pool.
     * @throws \InvalidArgumentException invalid key
     * @return bool True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        if (empty($keys)) {
            throw new \InvalidArgumentException('Array of keys to delete is empty');
        }

        $deleted = true;
        foreach ($keys as $key) {
            $this->checkIsLegalKey($key);
            $deleted = ($this->deleteItem($key) && $deleted);
        }
        return $deleted;
    }

    /**
     * Persists a cache item immediately.
     * @param PsrCacheItemInterface $item The cache item to save.
     * @return bool True if the item was successfully persisted. False if there was an error.
     */
    public function save(PsrCacheItemInterface $item)
    {
        /** @var $item CacheItemInterface */
        return $this->redisCache->set($item->getKey(), $item->get(), $item->getTtl());
    }

    /**
     * Sets a cache item to be persisted later.
     * @param PsrCacheItemInterface $item The cache item to save.
     * @return bool False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(PsrCacheItemInterface $item)
    {
//        throw new \Exception("saveDeferred is not implemented");
        return false;
    }

    /**
     * Persists any deferred cache items.
     * @return bool True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
//        throw new \Exception("commit is not implemented");
        return false;
    }
}