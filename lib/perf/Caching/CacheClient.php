<?php

namespace perf\Caching;

/**
 * This class allows to store any kind of content (text, html, object, etc) into cache.
 *
 */
class CacheClient
{

    /**
     * Cache storage.
     *
     * @var Storage
     */
    private $storage;

    /**
     *
     *
     * @var int
     */
    private $nowTimestamp;

    /**
     * Constructor.
     *
     * @param Storage $storage
     * @return void
     */
    public function __construct(Storage $storage)
    {
        $this->storage      = $storage;
        $this->nowTimestamp = time();
    }

    /**
     *
     *
     * @param int $timestamp
     * @return CacheClient Fluent return.
     */
    public function setNowTimestamp($timestamp)
    {
        $this->nowTimestamp = (int) $timestamp;

        return $this;
    }

    /**
     * Attempts to store provided content into cache. Cache file will hold creation and expiration timestamps,
     *   and provided content.
     *
     * @param mixed $id Cache item unique identifier (ex: 123).
     * @param mixed $data Content to be added to cache.
     * @param null|int $maxLifetimeSeconds (Optional) duration in seconds after which cache file will be
     *      considered expired.
     * @return CacheClient Fluent return.
     * @throws \RuntimeException
     */
    public function store($id, $data, $maxLifetimeSeconds = null)
    {
        $creationTimestamp = time();

        if (is_null($maxLifetimeSeconds)) {
            $expirationTimestamp = null;
        } else {
            $expirationTimestamp = ($creationTimestamp + (int) $maxLifetimeSeconds);
        }

        $entry = new CacheEntry($id, $data, $creationTimestamp, $expirationTimestamp);

        $this->storage->store($entry);

        return $this;
    }

    /**
     * Attempts to retrieve data from cache.
     *
     * @param mixed $id Cache entry unique identifier (ex: "123").
     * @param null|int $maxLifetimeSeconds Duration in seconds. If provided, will bypass expiration timestamp
     *      in cache file, using creation timestamp + provided duration to check whether cached content has
     *      expired or not.
     * @return null|mixed
     */
    public function tryFetch($id, $maxLifetimeSeconds = null)
    {
        $entry = $this->storage->tryFetch($id);

        if (is_null($entry)) {
            return null;
        }

        if (!is_null($maxLifetimeSeconds)) {
            if (($this->nowTimestamp - $entry->creationTimestamp()) > $maxLifetimeSeconds) {
                return null;
            }
        }

        if (!is_null($entry->expirationTimestamp())) {
            if ($this->nowTimestamp > $entry->expirationTimestamp()) {
                return null;
            }
        }

        return $entry->data();
    }

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return CacheClient Fluent return.
     */
    public function flushById($id)
    {
        $this->storage->flushById($id);

        return $this;
    }

    /**
     * Deletes every cache file.
     *
     * @return CacheClient Fluent return.
     */
    public function flushAll()
    {
        $this->storage->flushAll();

        return $this;
    }
}
