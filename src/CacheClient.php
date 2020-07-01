<?php

namespace perf\Caching;

use InvalidArgumentException;
use perf\Caching\Storage\CachingStorageInterface;
use RuntimeException;
use perf\Timing\Clock;

/**
 * Allows to store any kind of content (text, html, object, etc) into cache.
 */
class CacheClient
{
    private CachingStorageInterface $storage;

    private Clock $clock;

    public static function createWithStorage(CachingStorageInterface $storage): self
    {
        return static::createBuilder()
            ->setStorage($storage)
            ->build()
        ;
    }

    public static function createBuilder(): CacheClientBuilder
    {
        return new CacheClientBuilder();
    }

    public function __construct(CachingStorageInterface $storage, Clock $clock)
    {
        $this->storage = $storage;
        $this->clock   = $clock;
    }

    /**
     * Attempts to store provided content into cache. Cache file will hold creation and expiration timestamps,
     *   and provided content.
     *
     * @param mixed $id      Cache item unique identifier (ex: 123).
     * @param mixed $content Content to be added to cache.
     * @param null|int       $maxLifetimeSeconds (Optional) duration in seconds after which cache file will be
     *      considered expired.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function store(string $id, $content, ?int $maxLifetimeSeconds = null): void
    {
        $creationTimestamp = $this->clock->getTimestamp();

        if (null === $maxLifetimeSeconds) {
            $expirationTimestamp = null;
        } else {
            if ($maxLifetimeSeconds < 1) {
                throw new InvalidArgumentException('Invalid maximum lifetime.');
            }

            $expirationTimestamp = ($creationTimestamp + $maxLifetimeSeconds);
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
            throw new InvalidArgumentException('Invalid maximum lifetime.');
        }

        if ($entry->hasExpirationTimestamp()) {
            if ($nowTimestamp > $entry->getExpirationTimestamp()) {
                return null;
            }
        }

        return $entry->getContent();
    }

    public function flushById(string $id): void
    {
        $this->storage->flushById($id);
    }

    public function flushAll(): void
    {
        $this->storage->flushAll();
    }
}
