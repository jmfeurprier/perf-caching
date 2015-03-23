<?php

namespace perf\Caching;

/**
 *
 *
 */
class NullStorage implements Storage
{

    /**
     * Attempts to store provided cache entry into storage.
     *
     * @param CacheEntry $entry
     * @return void
     */
    public function store(CacheEntry $entry)
    {
    }

    /**
     *
     *
     * @param string $id Cache item unique identifier (ex: 123).
     * @return null|CacheEntry
     */
    public function tryFetch($id)
    {
        return null;
    }

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return void
     */
    public function flushById($id)
    {
    }

    /**
     * Deletes every cache file.
     *
     * @return void
     */
    public function flushAll()
    {
    }
}
