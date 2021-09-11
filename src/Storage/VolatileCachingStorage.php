<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;

class VolatileCachingStorage implements CachingStorageInterface
{
    /**
     * @var {string:CacheEntry}
     */
    private array $storedEntries = [];

    /**
     * {@inheritDoc}
     */
    public function store(CacheEntry $cacheEntry): void
    {
        $this->storedEntries[$cacheEntry->getId()] = $cacheEntry;
    }

    /**
     * {@inheritDoc}
     */
    public function tryFetch(string $id): ?CacheEntry
    {
        return $this->storedEntries[$id] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function flushById(string $id): void
    {
        unset($this->storedEntries[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function flushAll(): void
    {
        $this->storedEntries = [];
    }
}
