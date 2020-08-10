<?php

namespace perf\Caching;

use perf\Caching\Storage\CachingStorageInterface;
use perf\Caching\Storage\NullCachingStorage;
use perf\Timing\ClockInterface;
use perf\Timing\Clock;

class CacheClientBuilder
{
    private CachingStorageInterface $storage;

    private ClockInterface $clock;

    public function setStorage(CachingStorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function setClock(ClockInterface $clock): self
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

    private function getClock(): ClockInterface
    {
        if (empty($this->clock)) {
            return new Clock();
        }

        return $this->clock;
    }
}
