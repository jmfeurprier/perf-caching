<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;

class NullCachingStorage implements CachingStorageInterface
{
    public function store(CacheEntry $entry): void
    {
    }

    public function tryFetch(string $id): ?CacheEntry
    {
        return null;
    }

    public function flushById(string $id): void
    {
    }

    public function flushAll(): void
    {
    }
}
