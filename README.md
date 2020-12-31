Memcache Wrapper
=============

Use Memcache records as they were usual files.

# Installation

```json
{
    "require": {
        "alex-kalanis/memcache-wrapper": "1.0"
    }
}
```

(Refer to [Composer Documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction) if you are not
familiar with composer)

# Usages

Just only initialize wrapper with your Memcache instance.

Memcache module:

```php
    $memcache = new \MemcachePool();
    MemcacheWrapper::setPool($memcache);
    MemcacheWrapper::register();
```

Then work something like following:

```php
    file_get_contents('memcache://any/key/in/memcache/storage');
    file_put_contents('memcache://another/key/in/storage', 'add something');
```

For using storage just look onto kv_storage readme.
