<?php

namespace perf\Caching;

use perf\Caching\Storage\CachingStorageInterface;
use perf\Caching\Storage\NullCachingStorage;
use perf\Timing\Clock;
use perf\Timing\RealTimeClock;

class CacheClientBuilder
{
    private CachingStorageInterface $storage;

    private Clock $clock;

    public function setStorage(CachingStorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function setClock(Clock $clock): self
    {
        $this->clock = $clock;

        return $this;
    }

    public function build(): CacheClient
    {
        return new CacheClient($this->getStorage(), $this->getClock());
    }

    private function getStorage(): CachingStorageInterface
    {
        if (empty($this->storage)) {
            return new NullCachingStorage();
        }

        return $this->storage;
    }

    private function getClock(): Clock
    {
        if (empty($this->clock)) {
            return new RealTimeClock();
        }

        return $this->clock;
    }
}
