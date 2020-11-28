<?php

namespace MemcacheWrapper\Wrapper;


use Memcached;
use Traversable;


/**
 * Trait for add Memcache extension connector into the classes
 */
trait TMemcache
{
    protected $memcache = null;

    public function __construct(Memcached $memcache)
    {
        $this->memcache = $memcache;
    }

    protected function scan(string $mask): Traversable
    {
        $conf = $this->memcache->getServerList();
        $first = reset($conf); // get first server conf, pray that there is no more servers
        $socket = @fsockopen($first[0], $first[1]);

        if ($socket === false) {
            return;
        }
        $outcome = @fwrite($socket, 'lru_crawler metadump all' . chr(10));
        if ($outcome === false) {
            return;
        }
        $matches = [];
        while (($line = @fgets($socket, 1024)) !== false) {
            $line = trim($line);
            if ($line === 'END') {
                break;
            }
            $outcome = preg_match('/^key=([^\s]+)\s/', $line, $matches);
            if ($outcome !== 1) {
                return;
            }
            $match = urldecode($matches[1]);
            if (empty($mask)) {
                yield $match;
            } elseif (preg_match("#$mask#u", $match)) {
                yield $match;
            }
        }
        @fclose($socket);
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
