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
     * @var string
     */
    private $host;

    /**
     *
     *
     * @var int
     */
    private $port;

    /**
     *
     *
     * @var string
     */
    private $keyPrefix;

    /**
     *
     *
     * @var bool
     */
    private $connected = false;

    /**
     *
     *
     * @var Memcached
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int $port
     * @param string $keyPrefix
     * @return void
     */
    public function __construct($host, $port, $keyPrefix)
    {
        $this->host      = (string) $host;
        $this->port      = (int) $port;
        $this->keyPrefix = (string) $keyPrefix;
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
        if (null === $entry->expirationTimestamp()) {
            $expirationSeconds = 0; // Never expires.
        } else {
            $expirationSeconds = ($entry->expirationTimestamp() - $entry->creationTimestamp());
        }

        if (!$this->getConnection()->set($entry->id(), $entry, $expirationSeconds)) {
            throw new \RuntimeException('Failed to store data into memcache server.');
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
        $result = $this->getConnection()->get($id);

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
        $this->getConnection()->delete($id);

        static $expectedResultCodes = array(
            \Memcached::RES_SUCCESS,
            \Memcached::RES_NOTFOUND,
        );

        if (!in_array($this->getConnection()->getResultCode(), $expectedResultCodes, true)) {
            throw new \RuntimeException('Failed to delete data from memcache server.');
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
        if (!$this->getConnection()->flush()) {
            throw new \RuntimeException('Failed to flush memcache content.');
        }
    }

    /**
     *
     *
     * @return \Memcached
     */
    private function getConnection()
    {
        if (!$this->connected) {
            $memcached = new \Memcached();
            $memcached->addServer($this->host, $this->port);
            $memcached->setOption(\Memcached::OPT_PREFIX_KEY, $this->keyPrefix);

            $this->connection = $memcached;
            $this->connected  = true;
        }

        return $this->connection;
    }
}
