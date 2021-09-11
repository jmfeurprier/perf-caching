<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;
use perf\Caching\Exception\CachingException;

interface CachingStorageInterface
{
    /**
     * Attempts to store provided content into cache.
     * Stored entry will hold creation and expiration timestamps, and provided content.
     *
     * @throws CachingException
     */
    public function store(CacheEntry $cacheEntry): void;

    /**
     * @param string $id Cache item unique identifier (ex: 123).
     *
     * @throws CachingException
     */
    public function tryFetch(string $id): ?CacheEntry;

    /**
     * @throws CachingException
     */
    public function flushById(string $id): void;

    /**
     * @throws CachingException
     */
    public function flushAll(): void;
}
