<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;
use perf\Caching\Exception\CachingException;

interface CachingStorageInterface
{
    /**
     * Attempts to store provided content into cache.
     * Cache file will hold creation and expiration timestamps, and provided content.
     *
     * @param CacheEntry $cacheEntry
     *
     * @return void
     *
     * @throws CachingException
     */
    public function store(CacheEntry $cacheEntry): void;

    /**
     * @param string $id Cache item unique identifier (ex: 123).
     *
     * @return null|CacheEntry
     *
     * @throws CachingException
     */
    public function tryFetch(string $id): ?CacheEntry;

    /**
     * @param string $id
     *
     * @return void
     *
     * @throws CachingException
     */
    public function flushById(string $id): void;

    /**
     * @return void
     *
     * @throws CachingException
     */
    public function flushAll(): void;
}
