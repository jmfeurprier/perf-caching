<?php

namespace perf\Caching;

use perf\Timing\Clock;
use perf\Timing\RealTimeClock;

/**
 *
 *
 */
class CacheClientBuilder
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
     * @var Clock
     */
    private $clock;

    /**
     * Constructor.
     *
     * @param Storage $storage
     * @return CacheClientBuilder Fluent return.
     */
    public function setStorage(Storage $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     *
     *
     * @param Clock $clock
     * @return CacheClientBuilder Fluent return.
     */
    public function setClock(Clock $clock)
    {
        $this->clock = $clock;

        return $this;
    }

    /**
     *
     *
     * @return CacheClient
     */
    public function build()
    {
        return new CacheClient($this->getStorage(), $this->getClock());
    }

    /**
     *
     *
     * @return Storage
     */
    public function getStorage()
    {
        if ($this->storage) {
            return $this->storage;
        }

        return new NullStorage();
    }

    /**
     *
     *
     * @return Clock
     */
    public function getClock()
    {
        if ($this->clock) {
            return $this->clock;
        }

        return new RealTimeClock();
    }
}
