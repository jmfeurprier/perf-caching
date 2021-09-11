<?php

namespace perf\Caching;

use perf\Caching\Storage\CachingStorageInterface;
use perf\Timing\ClockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CacheClientTest extends TestCase
{
    /**
     * @var CachingStorageInterface|MockObject
     */
    private $storage;

    /**
     * @var ClockInterface|MockObject
     */
    private $clock;

    private CacheClient $cacheClient;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(CachingStorageInterface::class);

        $this->clock = $this->createMock(ClockInterface::class);

        $this->cacheClient = new CacheClient($this->storage, $this->clock);
    }

    public function testStoreAndFetch(): void
    {
        $id           = 'foo';
        $content      = 'bar';
        $nowTimestamp = 123;

        $cacheEntry = $this->createMock(CacheEntry::class);
        $cacheEntry->expects($this->once())->method('getContent')->willReturn($content);

        $this->storage->expects($this->once())->method('store');
        $this->storage->expects($this->once())->method('tryFetch')->with($id)->will($this->returnValue($cacheEntry));

        $this->clock->expects($this->atLeastOnce())->method('getTimestamp')->will($this->returnValue($nowTimestamp));

        $this->cacheClient->store($id, $content);

        $result = $this->cacheClient->tryFetch($id);

        $this->assertSame($content, $result);
    }
}
