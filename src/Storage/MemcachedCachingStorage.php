<?php

namespace perf\Caching\Storage;

use Memcached;
use perf\Caching\CacheEntry;
use perf\Caching\Exception\CachingException;
use RuntimeException;

class MemcachedCachingStorage implements CachingStorageInterface
{
    /**
     * @var Memcached
     */
    private $connection;

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

    public function store(CacheEntry $entry): void
    {
        if ($entry->hasExpirationTimestamp()) {
            $expirationTimestamp = $entry->getExpirationTimestamp();
        } else {
            $expirationTimestamp = 0; // Never expires.
        }

        if (!$this->connection->set($entry->getId(), $entry, $expirationTimestamp)) {
            $code    = $this->connection->getResultCode();
            $message = $this->connection->getResultMessage();

            $this->failure("Failed to store cache entry into Memcached server: #{$code} - {$message}");
        }
    }

    public function tryFetch(string $id): ?CacheEntry
    {
        $result = $this->connection->get($id);

        if (false === $result) {
            return null;
        }

        return $result;
    }

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

    public function flushAll(): void
    {
        if (!$this->connection->flush()) {
            $this->failure('Failed to flush Memcached content.');
        }
    }

    /**
     * @param string $message
     *
     * @return void
     *
     * @throws CachingException
     */
    private function failure(string $message): void
    {
        $resultCode    = $this->connection->getResultCode();
        $resultMessage = $this->connection->getResultMessage();

        throw new CachingException("{$message} << #{$resultCode} {$resultMessage}");
    }
}
