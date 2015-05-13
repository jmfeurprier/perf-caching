<?php

namespace perf\Caching;

/**
 *
 *
 *
 */
interface Storage
{

    /**
     * Attempts to store provided content into cache.
     * Cache file will hold creation and expiration timestamps, and provided content.
     *
     * @param CacheEntry $entry
     * @return void
     * @throws \RuntimeException
     */
    public function store(CacheEntry $entry);

    /**
     *
     *
     * @param string $id Cache item unique identifier (ex: 123).
     * @return null|CacheEntry
     * @throws \RuntimeException
     */
    public function tryFetch($id);

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return void
     */
    public function flushById($id);

    /**
     * Deletes every cache file.
     *
     * @return void
     */
    public function flushAll();
}
