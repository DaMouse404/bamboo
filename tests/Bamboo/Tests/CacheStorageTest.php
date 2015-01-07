<?php

namespace Bamboo\Tests;

use Bamboo\CacheStorage;
use Bamboo\CacheInterface;
use \Guzzle\Http\EntityBody;

class CacheStorageTest extends BambooClientTestCase
{

    public function testBodyKey() {
        $prefix = 'bambootest_';
        $body = 'This will not be part of the hash key';
        $url = 'http://localhost/testPath';
        $expects = $prefix . md5($url);

        $mockCache = $this->getMockBuilder('\Guzzle\Cache\CacheAdapterInterface')->getMock();
        $mockCache->expects($this->at(1))
            ->method('save')
            ->with($expects);

        $cacheStorage = new CacheStorage($mockCache, $prefix);
        $cacheStorage->cache(
            new \Guzzle\Http\Message\Request('GET', $url),
            new \Guzzle\Http\Message\Response(200, array(), $body)
        );
    }

}
