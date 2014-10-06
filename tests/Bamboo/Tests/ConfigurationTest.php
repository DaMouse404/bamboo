<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\Configuration;

class ConfigurationTest extends BambooTestCase
{
    public function tearDown() {
        Configuration::setBaseUrl('');
        Configuration::setCache(false);
        Configuration::setConfig(array());
        Configuration::setFakeHttpClient(null);
        Configuration::setFailHttpClient(null);
        Configuration::setHost('');
        Configuration::setPlaceholderImageUrl('');
        Configuration::setNetworkProxy('');
    }

    public function testBaseUrlGetterAndSetter() {
        Configuration::setBaseUrl('Bake Cake');
        $this->assertEquals('Bake Cake', Configuration::getBaseUrl());
    }

    public function testConfigGetterAndSetter() {
        Configuration::setConfig('Make Cake');
        $this->assertEquals('Make Cake', Configuration::getConfig());
    }

    public function testCounterGetterAndSetter() {
        $mockCounter = new MockCounter;
        Configuration::setCounter($mockCounter);
        $this->assertInstanceOf('Bamboo\Tests\MockCounter', Configuration::getCounter());
    }

    public function testFakeHttpGetterAndSetter() {
        Configuration::setFakeHttpClient('Quake Cake');
        $this->assertEquals('Quake Cake', Configuration::getFakeHttpClient());
    }

    public function testFailHttpGetterAndSetter() {
        Configuration::setFailHttpClient('Snake Cake');
        $this->assertEquals('Snake Cake', Configuration::getFailHttpClient());
    }

    public function testHostGetterAndSetter() {
        Configuration::setHost('Take Cake');
        $this->assertEquals('Take Cake', Configuration::getHost());
    }

    public function testLoggerGetterAndSetter() {
        $mockLogger = new MockConfigurationLogger;
        Configuration::setLogger($mockLogger);
        $this->assertInstanceOf('Bamboo\Tests\MockConfigurationLogger', Configuration::getLogger());
    }

    public function testNetworkProxyGetterAndSetter() {
        Configuration::setNetworkProxy('Drake Cake');
        $this->assertEquals('Drake Cake', Configuration::getNetworkProxy());
    }

    public function testSetCache() {
        $cache = $this->getMockBuilder('Guzzle\Cache\CacheAdapterInterface')
                    ->getMockForAbstractClass();

        Configuration::setCache($cache);

        $client = Client::getInstance()->getClient('atoz');

        $l = $client->getEventDispatcher()->getListeners();

        // Check that the cache plugin was properly attached
        $this->assertInstanceOf('Guzzle\Plugin\Cache\CachePlugin', $l['request.sent'][0][0]);
    }
}

class MockCounter {}

class MockConfigurationLogger {
    public function log() {}
}
