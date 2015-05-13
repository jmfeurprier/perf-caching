<?php

namespace perf\Caching;

/**
 *
 */
class CacheClientBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    protected function setUp()
    {
        $this->cacheClientBuilder = new CacheClientBuilder();
    }

    /**
     *
     */
    public function testBuildBare()
    {
        $result = $this->cacheClientBuilder->build();

        $this->assertInstanceOf('\\perf\\Caching\\CacheClient', $result);
    }

    /**
     *
     */
    public function testBuildWithStorage()
    {
        $storage = $this->getMock('\\perf\\Caching\\Storage');

        $this->cacheClientBuilder->setStorage($storage);

        $result = $this->cacheClientBuilder->build();

        $this->assertInstanceOf('\\perf\\Caching\\CacheClient', $result);
    }

    /**
     *
     */
    public function testBuildWithClock()
    {
        $clock = $this->getMock('\\perf\\Timing\\Clock');

        $this->cacheClientBuilder->setClock($clock);

        $result = $this->cacheClientBuilder->build();

        $this->assertInstanceOf('\\perf\\Caching\\CacheClient', $result);
    }
}
