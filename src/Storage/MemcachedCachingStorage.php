<?php

namespace perf\Caching\Storage;

use Memcached;
use perf\Caching\CacheEntry;
use perf\Caching\Exception\CachingException;

class MemcachedCachingStorage implements CachingStorageInterface
{
    private Memcached $connection;

    public static function createFromCredentials(string $host, int $port, string $keyPrefix = null): self
    {
        $memcached = new Memcached();
        $memcached->addServer($host, $port);

        if (null !== $keyPrefix) {
            $memcached->setOption(Memcached::OPT_PREFIX_KEY, $keyPrefix);
        }

        return new self($memcached);
    }

    public static function createFromMemcached(Memcached $memcached): self
    {
        return new self($memcached);
    }

    private function __construct(Memcached $memcached)
    {
        $this->connection = $memcached;
    }

    /**
     * {@inheritDoc}
     */
    public function store(CacheEntry $cacheEntry): void
    {
        $expirationTimestamp = $this->getExpirationTimestamp($cacheEntry);

        if (!$this->connection->set($cacheEntry->getId(), $cacheEntry, $expirationTimestamp)) {
            $code    = $this->connection->getResultCode();
            $message = $this->connection->getResultMessage();

            $this->failure("Failed to store cache entry into Memcached server: #{$code} - {$message}");
        }
    }

    private function getExpirationTimestamp(CacheEntry $entry): int
    {
        if ($entry->hasExpirationTimestamp()) {
            return $entry->getExpirationTimestamp();
        }

        return 0; // Never expires.
    }

    /**
     * {@inheritDoc}
     */
    public function tryFetch(string $id): ?CacheEntry
    {
        $result = $this->connection->get($id);

        if (false === $result) {
            return null;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function flushById(string $id): void
    {
        $expectedResultCodes = [
            Memcached::RES_SUCCESS,
            Memcached::RES_NOTFOUND,
        ];

        $this->connection->delete($id);

        if (!in_array($this->connection->getResultCode(), $expectedResultCodes, true)) {
            $this->failure('Failed to delete cache entry from Memcached server.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function flushAll(): void
    {
        if (!$this->connection->flush()) {
            $this->failure('Failed to flush Memcached content.');
        }
    }

    /**
     * @throws CachingException
     */
    private function failure(string $message): void
    {
        $resultCode    = $this->connection->getResultCode();
        $resultMessage = $this->connection->getResultMessage();

        throw new CachingException("{$message} << #{$resultCode} {$resultMessage}");
    }
}
