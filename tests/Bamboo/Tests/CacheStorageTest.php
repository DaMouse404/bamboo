<?php

namespace Bamboo\Tests;

use Bamboo\CacheStorage;
use Bamboo\CacheInterface;
use \Guzzle\Http\EntityBody;

class CacheStorageTest extends BambooClientTestCase
{

    const PREFIX = 'bambootest_';
    const BODY = 'This will not be part of the hash key';
    const URL = 'http://localhost/testPath';

    private $_mockCache;

    public function setUp() {
        $this->_mockCache = $this->getMockBuilder('\Guzzle\Cache\CacheAdapterInterface')->getMock();
    }

    /**
     * The body cache key is md5(url), parent's also include the body digest
     */
    public function testBodyKey() {
        $expectsBody = self::PREFIX . md5(self::URL);
        $this->_mockCache->expects($this->at(1))
            ->method('save')
            ->with($expectsBody);
        $this->_makeRequest();
    }

    /**
     * The header cache key is md5(method + ' ' + url)
     */
    public function testHeaderKey() {
        $expectsHeader = self::PREFIX . md5('GET ' . self::URL);
        $this->_mockCache->expects($this->at(2))
            ->method('save')
            ->with($expectsHeader);
        $this->_makeRequest();
    }

    private function _makeRequest() {
        $cacheStorage = new CacheStorage($this->_mockCache, self::PREFIX);
        $cacheStorage->cache(
            new \Guzzle\Http\Message\Request('GET', self::URL),
            new \Guzzle\Http\Message\Response(200, array(), self::BODY)
        );
    }

}

