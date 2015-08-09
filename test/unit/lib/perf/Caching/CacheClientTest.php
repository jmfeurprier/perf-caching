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
        $data         = 'bar';
        $nowTimestamp = 123;

        $cacheEntry = $this->getMockBuilder('\\perf\\Caching\\CacheEntry')->disableOriginalConstructor()->getMock();
        $cacheEntry->expects($this->once())->method('data')->will($this->returnValue($data));

        $storage = $this->getMock('\\perf\\Caching\\Storage');
        $storage->expects($this->once())->method('store');
        $storage->expects($this->once())->method('tryFetch')->with($id)->will($this->returnValue($cacheEntry));

        $cacheClient = new CacheClient($storage);
        $cacheClient->setNowTimestamp($nowTimestamp);

        $cacheClient->store($id, $data);

        $result = $cacheClient->tryFetch($id);

        $this->assertSame($data, $result);
    }
}
