perf caching
============

Allows caching of data (with Memcached, file-system, etc).

## Usage

### Initialization

```php
<?php

use perf\Caching\CacheClient;
use perf\Caching\Storage\FileSystemCachingStorage;
use perf\Caching\Storage\MemcachedCachingStorage;
use perf\Caching\Storage\NullCachingStorage;
use perf\Caching\Storage\VolatileCachingStorage;

// Memcached
$storage = MemcachedCachingStorage::createFromCredentials('1.2.3.4', 123);
$cache   = CacheClient::createWithStorage($storage);

// Volatile storage
$storage = new VolatileCachingStorage();
$cache   = CacheClient::createWithStorage($storage);

// File-system storage
$storage = new FileSystemCachingStorage('/tmp/cache');
$cache   = CacheClient::createWithStorage($storage);

// Null storage (caches nothing)
$storage = new NullCachingStorage();
$cache   = CacheClient::createWithStorage($storage);
```

### Storing and retrieving data

```php
<?php

$objectToStore = new \stdClass();
$objectToStore->bar = 'baz';

$cache->store('foo', $objectToStore);

// ...

$object = $cache->tryFetch('foo');
```
