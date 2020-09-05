<?php

namespace MemcacheWrapper\Wrapper;

use MemcachePool;

/**
 * Trait for add Memcache extension connector into the classes
 */
trait TMemcache
{
    protected $memcache = null;

    public function __construct(MemcachePool $memcache)
    {
        $this->memcache = $memcache;
    }

    protected function scan(string $mask)
    {
        $allSlabs = $this->memcache->getExtendedStats('slabs');
        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs AS $slabId => $slabMeta) {
                $cdump = $this->memcache->getExtendedStats('cachedump', (int) $slabId);
                foreach ($cdump AS $keys => $arrVal) {
                    if (!is_array($arrVal)) {
                        continue;
                    }
                    foreach ($arrVal as $k => $v) {
                        if (preg_match("#$mask#u", $k)) {
                            yield $k;
                        }
                    }
                }
            }
        }
    }

    protected function get(string $key): string
    {
        return (string)$this->memcache->get($key);
    }

    protected function append(string $key, string $value): int
    {
        $data = $this->get($key) . $value;
        return $this->memcache->set($key, $data);
    }

    protected function del(string $key): bool
    {
        return (bool)$this->memcache->delete($key);
    }
}
