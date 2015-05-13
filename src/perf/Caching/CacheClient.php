<?php

namespace perf\Caching;

use perf\Timing\Clock;

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
     * Clock.
     *
     * @var Clock
     */
    private $clock;

    /**
     * Creates a new cache client with default configuration.
     *
     * @return CacheClient
     */
    public static function createDefault()
    {
        return static::createBuilder()->build();
    }

    /**
     * Creates a new cache client with volatile storage.
     *
     * @return CacheClient
     */
    public static function createVolatile()
    {
        return static::createWithStorage(new VolatileStorage());
    }

    /**
     *
     *
     * @param Storage $storage
     * @return CacheClient
     */
    public static function createWithStorage(Storage $storage)
    {
        return static::createBuilder()
            ->setStorage($storage)
            ->build()
        ;
    }

    /**
     *
     *
     * @return CacheClientBuilder
     */
    public static function createBuilder()
    {
        return new CacheClientBuilder();
    }

    /**
     * Constructor.
     *
     * @param Storage $storage
     * @param Clock $clock
     * @return void
     */
    public function __construct(Storage $storage, Clock $clock)
    {
        $this->storage = $storage;
        $this->clock   = $clock;
    }

    /**
     * Attempts to store provided content into cache. Cache file will hold creation and expiration timestamps,
     *   and provided content.
     *
     * @param mixed $id Cache item unique identifier (ex: 123).
     * @param mixed $content Content to be added to cache.
     * @param null|int $maxLifetimeSeconds (Optional) duration in seconds after which cache file will be
     *      considered expired.
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function store($id, $content, $maxLifetimeSeconds = null)
    {
        $creationTimestamp = $this->clock->getTimestamp();

        if (null === $maxLifetimeSeconds) {
            $expirationTimestamp = null;
        } elseif (is_int($maxLifetimeSeconds)) {
            if ($maxLifetimeSeconds < 1) {
                throw new \InvalidArgumentException('Invalid maximum lifetime.');
            }

            $expirationTimestamp = ($creationTimestamp + $maxLifetimeSeconds);
        } else {
            throw new \InvalidArgumentException('Invalid maximum lifetime.');
        }

        $entry = new CacheEntry($id, $content, $creationTimestamp, $expirationTimestamp);

        $this->storage->store($entry);
    }

    /**
     * Attempts to retrieve content from cache.
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

        if (null === $entry) {
            return null;
        }

        $nowTimestamp = $this->clock->getTimestamp();

        if (is_int($maxLifetimeSeconds)) {
            $entryAgeSeconds = ($nowTimestamp - $entry->getCreationTimestamp());

            if ($entryAgeSeconds > $maxLifetimeSeconds) {
                return null;
            }
        } elseif (null !== $maxLifetimeSeconds) {
            throw new \InvalidArgumentException('Invalid maximum lifetime.');
        }

        if ($entry->hasExpirationTimestamp()) {
            if ($nowTimestamp > $entry->getExpirationTimestamp()) {
                return null;
            }
        }

        return $entry->getContent();
    }

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return void
     */
    public function flushById($id)
    {
        $this->storage->flushById($id);
    }

    /**
     * Deletes every cache file.
     *
     * @return void
     */
    public function flushAll()
    {
        $this->storage->flushAll();
    }
}
