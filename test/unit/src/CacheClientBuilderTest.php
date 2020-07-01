<?php

namespace perf\Caching;

use perf\Caching\Storage\CachingStorageInterface;
use perf\Timing\Clock;
use PHPUnit\Framework\TestCase;

class CacheClientBuilderTest extends TestCase
{
    private CacheClientBuilder $cacheClientBuilder;

    protected function setUp(): void
    {
        $this->cacheClientBuilder = new CacheClientBuilder();
    }

    public function testBuildBare()
    {
        $result = $this->cacheClientBuilder->build();

        $this->assertInstanceOf(CacheClient::class, $result);
    }

    public function testBuildWithStorage()
    {
        $storage = $this->createMock(CachingStorageInterface::class);

        $this->cacheClientBuilder->setStorage($storage);

        $result = $this->cacheClientBuilder->build();

        $this->assertInstanceOf(CacheClient::class, $result);
    }

    public function testBuildWithClock()
    {
        $clock = $this->createMock(Clock::class);

        $this->cacheClientBuilder->setClock($clock);

        $result = $this->cacheClientBuilder->build();

        $this->assertInstanceOf(CacheClient::class, $result);
    }
}
