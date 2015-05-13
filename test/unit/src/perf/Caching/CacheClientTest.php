<?php

namespace perf\Caching;

/**
 *
 */
class CacheClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testStoreAndFetch()
    {
        $id           = 'foo';
        $content      = 'bar';
        $nowTimestamp = 123;

        $cacheEntry = $this->getMockBuilder('\\perf\\Caching\\CacheEntry')->disableOriginalConstructor()->getMock();
        $cacheEntry->expects($this->once())->method('getContent')->will($this->returnValue($content));

        $storage = $this->getMock('\\perf\\Caching\\Storage');
        $storage->expects($this->once())->method('store');
        $storage->expects($this->once())->method('tryFetch')->with($id)->will($this->returnValue($cacheEntry));

        $clock = $this->getMock('\\perf\\Timing\\Clock');
        $clock->expects($this->atLeastOnce())->method('getTimestamp')->will($this->returnValue($nowTimestamp));

        $cacheClient = new CacheClient($storage, $clock);

        $cacheClient->store($id, $content);

        $result = $cacheClient->tryFetch($id);

        $this->assertSame($content, $result);
    }
}
