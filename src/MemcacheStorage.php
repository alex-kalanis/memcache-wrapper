<?php

namespace MemcacheWrapper;


use kalanis\kw_storage\Interfaces\IStorage;
use MemcacheWrapper\Wrapper\TMemcache;
use Traversable;


/**
 * Class Memcache
 * @package MemcacheWrapper
 * Memcache storage for k-v pairs
 *
 * add - set, fails for existing
 * replace - set, fails for missing
 * set - set value
 */
class MemcacheStorage implements IStorage
{
    use TMemcache;

    /** @var int */
    protected $timeout = 0;

    public function check(string $key): bool
    {
        return ($this->memcache->getStats() && $this->memcache->getResultCode() == '00' ); // 00 = MEMCACHED_SUCCESS
    }

    public function exists(string $key): bool
    {
        // cannot call exists() - get on non-existing key returns false
        return (false !== $this->memcache->get($key));
    }

    public function load(string $key): string
    {
        return $this->get($key);
    }

    public function save(string $key, $data, ?int $timeout = null): bool
    {
        return $this->memcache->set($key, $data, $timeout);
    }

    public function remove(string $key): bool
    {
        return $this->del($key);
    }

    public function lookup(string $key): Traversable
    {
        return $this->scan($key);
    }

    public function increment(string $key): bool
    {
        return (false !== $this->memcache->increment($key));
    }

    public function decrement(string $key): bool
    {
        return (false !== $this->memcache->decrement($key));
    }

    public function removeMulti(array $keys): array
    {
        return $this->memcache->deleteMulti($keys);
    }
}
