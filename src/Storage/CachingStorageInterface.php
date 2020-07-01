<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;
use perf\Caching\Exception\CachingException;
use RuntimeException;

interface CachingStorageInterface
{
    /**
     * Attempts to store provided content into cache.
     * Cache file will hold creation and expiration timestamps, and provided content.
     *
     * @param CacheEntry $entry
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function store(CacheEntry $entry): void;

    /**
     * @param string $id Cache item unique identifier (ex: 123).
     *
     * @return null|CacheEntry
     *
     * @throws RuntimeException
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
