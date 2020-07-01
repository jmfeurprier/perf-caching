<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;

class VolatileCachingStorage implements CachingStorageInterface
{
    /**
     * @var {string:CacheEntry}
     */
    private $storedEntries = [];

    public function store(CacheEntry $entry): void
    {
        $this->storedEntries[$entry->getId()] = $entry;
    }

    public function tryFetch(string $id): ?CacheEntry
    {
        return $this->storedEntries[$id] ?? null;
    }

    public function flushById(string $id): void
    {
        unset($this->storedEntries[$id]);
    }

    public function flushAll(): void
    {
        $this->storedEntries = [];
    }
}
