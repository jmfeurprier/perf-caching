<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;

class NullCachingStorage implements CachingStorageInterface
{
    /**
     * {@inheritDoc}
     */
    public function store(CacheEntry $cacheEntry): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function tryFetch(string $id): ?CacheEntry
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function flushById(string $id): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function flushAll(): void
    {
    }
}
