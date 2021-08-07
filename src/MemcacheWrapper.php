<?php

namespace kalanis\MemcacheWrapper;


use Memcached;
use kalanis\MemcacheWrapper\Wrapper\MemcacheException;


/**
 * Class MemcacheWrapper
 * @package kalanis\MemcacheWrapper
 * Wrapper to plug Memcache into PHP - by extension
 *
 * Usage:
 * - In initialization:
MemcacheWrapper::setPool($memcache);
MemcacheWrapper::register();
 * - somewhere in code:
file_get_contents('memcache://any/key/in/memcache/storage');
file_put_contents('memcache://another/key/in/storage', 'add something');
 */
class MemcacheWrapper
{
    /** @var Memcached|null */
    protected static $memcache = null;

    /** @var resource */
    public $context;

    /** @var Wrapper\Keys|null */
    protected $keyQuery = null;
    /** @var Wrapper\Data|null */
    protected $fileQuery = null;
    protected $showErrors = true;

    public static function setPool(Memcached $memcache): void
    {
        static::$memcache = $memcache;
    }

    public static function register()
    {
        if (in_array("memcache", stream_get_wrappers())) {
            stream_wrapper_unregister("memcache");
        }
        stream_wrapper_register("memcache", "\MemcacheWrapper\MemcacheWrapper");
    }

    public function __construct()
    {
        $this->keyQuery = new Wrapper\Keys(static::$memcache);
        $this->fileQuery = new Wrapper\Data(static::$memcache);
    }

    public function dir_closedir(): bool
    {
        try {
            return $this->keyQuery->close();
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    public function dir_opendir(string $path, int $options): bool
    {
        try {
            return $this->keyQuery->open($path, $options);
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @return string|false
     */
    public function dir_readdir()
    {
        try {
            return $this->keyQuery->read();
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    public function dir_rewinddir(): bool
    {
        try {
            return $this->keyQuery->rewind();
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param string $path
     * @param int $mode
     * @param int $options
     * @return bool
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        try {
            return $this->keyQuery->make($path, $mode, $options);
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param string $path_from
     * @param string $path_to
     * @return bool
     */
    public function rename(string $path_from, string $path_to): bool
    {
        try {
            return $this->keyQuery->rename($path_from, $path_to);
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param string $path
     * @param int $options
     * @return bool
     */
    public function rmdir(string $path, int $options): bool
    {
        try {
            return $this->keyQuery->remove($path, $options);
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /**
     * @param int $cast_as
     * @return resource|bool
     */
    public function stream_cast(int $cast_as)
    {
        try {
            return $this->fileQuery->stream_cast($cast_as);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_close(): void
    {
        try {
            $this->fileQuery->stream_close();
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
        }
    }

    public function stream_eof(): bool
    {
        try {
            return $this->fileQuery->stream_eof();
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return true;
        }
    }

    public function stream_flush(): bool
    {
        try {
            return $this->fileQuery->stream_flush();
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_lock(int $operation): bool
    {
        try {
            return $this->fileQuery->stream_lock($operation);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_metadata(string $path, int $option, $var): bool
    {
        try {
            return $this->fileQuery->stream_metadata($path, $option, $var);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_open(string $path, string $mode, int $options, string &$opened_path): bool
    {
        try {
            $this->canReport($options);
            return $this->fileQuery->stream_open($this->keyQuery, $path, $mode);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_read(int $count): string
    {
        try {
            return $this->fileQuery->stream_read($count);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        try {
            return $this->fileQuery->stream_seek($offset, $whence);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        try {
            return $this->fileQuery->stream_set_option($option, $arg1, $arg2);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_stat(): array
    {
        try {
            return $this->fileQuery->stream_stat($this->keyQuery);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return [];
        }
    }

    public function stream_tell(): int
    {
        try {
            return $this->fileQuery->stream_tell();
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return -1;
        }
    }

    public function stream_truncate(int $new_size): bool
    {
        try {
            return $this->fileQuery->stream_truncate($new_size);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return false;
        }
    }

    public function stream_write(string $data): int
    {
        try {
            return $this->fileQuery->stream_write($data);
        } catch (MemcacheException $ex) {
            $this->errorReport($ex);
            return 0;
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function unlink(string $path): bool
    {
        try {
            return $this->fileQuery->unlink($path);
        } catch (MemcacheException $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    protected function canReport($opts): void
    {
        $this->showErrors = ($opts & STREAM_REPORT_ERRORS);
    }

    /**
     * @param MemcacheException $ex
     */
    protected function errorReport(MemcacheException $ex): void
    {
        if ($this->showErrors) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
        }
    }

    public function url_stat(string $path, int $flags): array
    {
        try {
            return $this->keyQuery->stats($path, $flags);
        } catch (MemcacheException $ex) {
            if ($flags & ~STREAM_URL_STAT_QUIET) {
                trigger_error($ex->getMessage(), E_USER_ERROR);
            }
            return [
                0 => 0,
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => -1,
                12 => -1,
            ];
        }
    }
}
