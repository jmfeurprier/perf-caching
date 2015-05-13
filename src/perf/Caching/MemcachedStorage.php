<?php

namespace perf\Caching;

/**
 *
 *
 */
class MemcachedStorage implements Storage
{

    /**
     *
     *
     * @var \Memcached
     */
    private $connection;

    /**
     * Static constructor.
     *
     * @param string $host
     * @param int $port
     * @param null|string $keyPrefix
     * @return MemcachedStorage
     */
    public static function createFromCredentials($host, $port, $keyPrefix = null)
    {
        $memcached = new \Memcached();
        $memcached->addServer($host, $port);

        if (null !== $keyPrefix) {
            $memcached->setOption(\Memcached::OPT_PREFIX_KEY, $keyPrefix);
        }

        return new self($memcached);
    }

    /**
     * Static constructor.
     *
     * @param \Memcached $memcached
     * @return MemcachedStorage
     */
    public static function createFromMemcached(\Memcached $memcached)
    {
        return new self($memcached);
    }

    /**
     * Constructor.
     *
     * @param \Memcached $memcached
     * @return void
     */
    private function __construct(\Memcached $memcached)
    {
        $this->connection = $memcached;
    }

    /**
     * Attempts to store provided cache entry into storage.
     *
     * @param CacheEntry $entry
     * @return void
     * @throws \RuntimeException
     */
    public function store(CacheEntry $entry)
    {
        if ($entry->hasExpirationTimestamp()) {
            $expirationTimestamp = $entry->getExpirationTimestamp();
        } else {
            $expirationTimestamp = 0; // Never expires.
        }

        if (!$this->connection->set($entry->getId(), $entry, $expirationTimestamp)) {
            return $this->failure('Failed to store cache entry into Memcached server.');
        }
    }

    /**
     *
     *
     * @param string $id Cache item unique identifier (ex: 123).
     * @return null|CacheEntry
     * @throws \RuntimeException
     */
    public function tryFetch($id)
    {
        $result = $this->connection->get($id);

        if (false === $result) {
            return null;
        }

        return $result;
    }

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return void
     */
    public function flushById($id)
    {
        $this->connection->delete($id);

        static $expectedResultCodes = array(
            \Memcached::RES_SUCCESS,
            \Memcached::RES_NOTFOUND,
        );

        if (!in_array($this->connection->getResultCode(), $expectedResultCodes, true)) {
            return $this->failure('Failed to delete cache entry from Memcached server.');
        }
    }

    /**
     * Deletes every cache file.
     *
     * @return void
     * @throws \RuntimeException
     */
    public function flushAll()
    {
        if (!$this->connection->flush()) {
            return $this->failure('Failed to flush Memcached content.');
        }
    }

    /**
     *
     *
     * @param string $message
     * @return void
     * @throws \RuntimeException
     */
    private function failure($message)
    {
        $resultCode    = $this->connection->getResultCode();
        $resultMessage = $this->connection->getResultMessage();

        throw new \RuntimeException("{$message} << #{$resultCode} {$resultMessage}");
    }
}
