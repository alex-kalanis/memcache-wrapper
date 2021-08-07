<?php

namespace kalanis\MemcacheWrapper\Wrapper;


use Traversable;


/**
 * Class Keys
 * @package kalanis\MemcacheWrapper\Wrapper
 * Wrapper to plug Memcache info into PHP - directory part
 */
class Keys extends AOperations
{
    use TMemcache;

    protected $path = '';
    protected $seek = 0;
    protected $iterator = null;

    public function close(): bool
    {
        $this->seek = 0;
        return true;
    }

    public function open(string $path, int $options): bool
    {
        $this->path = $path;
        return true;
    }

    /**
     * @return string|bool
     */
    public function read()
    {
        $keyName = $this->searchKeys($this->parsePath($this->path));
        if (empty($keyName)) {
            return false;
        }
        return current($keyName);
    }

    public function rewind(): bool
    {
        $this->seek = 0;
        return true;
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     */
    public function make(string $path, int $mode, int $options): bool
    {
        return false;
    }

    /**
     * @param string $path
     * @param string $right
     * @param bool $allow
     * @return bool
     */
    public function rights(string $path, string $right, bool $allow): bool
    {
        return false;
    }

    /**
     * @param string $pathFrom
     * @param string $pathTo
     * @return bool
     */
    public function rename(string $pathFrom, string $pathTo): bool
    {
        return false;
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     */
    public function remove(string $path, int $options): bool
    {
        return false;
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array
     * @throws MemcacheException
     */
    public function stats(string $path, int $flags): array
    {
        while ($fileInfo = $this->searchKeys($this->parsePath($path))) {
            // seek into the name...
            if ($content = $this->get($fileInfo)) {
                return [
                    0 => 0,
                    1 => 0,
                    2 => 0100666,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0,
                    7 => strlen($content),
                    8 => 0,
                    9 => 0,
                    10 => 0,
                    11 => -1,
                    12 => -1,
                ];
            }
        }
        throw new MemcacheException('Memcache keys not found: ' . $path);
    }

    /**
     * @param string $path
     * @return Traversable
     */
    protected function searchKeys(string $path): Traversable
    {
        return $this->scan($this->parsePath($path));
    }
}
