<?php

namespace perf\Caching;

/**
 *
 *
 */
class VolatileStorage implements Storage
{

    /**
     * Cached entries.
     *
     * @var {string:CacheEntry}
     */
    private $entries = array();

    /**
     * Attempts to store provided cache entry into storage.
     *
     * @param CacheEntry $entry
     * @return void
     */
    public function store(CacheEntry $entry)
    {
        $this->entries[$entry->id()] = $entry;
    }

    /**
     *
     *
     * @param string $id Cache item unique identifier (ex: 123).
     * @return null|CacheEntry
     */
    public function tryFetch($id)
    {
        if (!array_key_exists($id, $this->entries)) {
            return null;
        }

        return $this->entries[$id];
    }

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return void
     */
    public function flushById($id)
    {
        unset($this->entries[$id]);
    }

    /**
     * Deletes every cache file.
     *
     * @return void
     */
    public function flushAll()
    {
        $this->entries = array();
    }
}
