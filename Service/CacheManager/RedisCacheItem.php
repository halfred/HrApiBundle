<?php

namespace Hr\ApiBundle\Service\CacheManager;

use App\Interfaces\CacheItemInterface;

/**
 * Class RedisCacheItem
 * @package App\Service\CacheManager
 */
class RedisCacheItem implements CacheItemInterface
{
    /** @var string */
    protected $key;
    /** @var string */
    protected $value;
    /** @var bool */
    protected $isHit;
    /** @var \DateTimeInterface|null */
    protected $expirationAt;
    /** @var int|null */
    protected $expirationAfter;

    /**
     * RedisCacheItem constructor.
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Returns the key for the current cache item.
     * @return string The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     * The value returned must be identical to the value originally stored by set().
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     * @return mixed The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     * @return bool True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * @param bool $isHit
     */
    public function setIsHit(bool $isHit)
    {
        $this->isHit = $isHit;
    }

    /**
     * Sets the value represented by this cache item.
     * @param mixed $value The serializable value to be stored.
     * @return static The invoked object.
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     * @param \DateTimeInterface|null $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     * @throws \InvalidArgumentException invalid expiration parameter
     * @return static The called object.
     */
    public function expiresAt($expiration = null)
    {
        if (is_null($expiration)) {
            $this->expirationAt = null;
        } elseif (get_class($expiration) == '\DateTimeInterface') {
            $this->expirationAt    = $expiration;
            $this->expirationAfter = null;
        } else {
            throw new \InvalidArgumentException('Invalid expiration parameter type');
        }
        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter($time)
    {
        if (is_null($time)) {
            $this->expirationAfter = null;
        } elseif (is_int($time)) {
            $this->expirationAfter = $time;
            $this->expirationAt    = null;
        } elseif (is_string($time) && ((int)$time == $time)) {
            $this->expirationAfter = (int)$time;
            $this->expirationAt    = null;
        } elseif (is_object($time) && (get_class($time) == '\DateInterval')) {
            /** @var \DateInterval expirationAfter */
            $this->expirationAfter = $time->format('s');
            $this->expirationAt    = null;
        } else {
            throw new \InvalidArgumentException('Invalid expiration parameter type');
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTtl(): ?int
    {
        if (!is_null($this->expirationAfter)) {
            return $this->expirationAfter;
        } elseif (!is_null($this->expirationAt)) {
            $dateInterval = (new \DateTime)->diff($this->expirationAt);
            $ttl          = (int)$dateInterval->format('Rs');
            return ($ttl > 0) ? $ttl : 0;
        } else {
            return null;
        }
    }


}