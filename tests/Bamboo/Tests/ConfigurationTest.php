<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\Configuration;

class ConfigurationTest extends BambooBaseTestCase
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

    public function testImageHostGetterAndSetter() {
        Configuration::setImageHost('Take Image Cake');
        $this->assertEquals('Take Image Cake', Configuration::getImageHost());
        Configuration::setImageHost(false);
    }

    public function tesSetLogger() {
        $mockLogger = new MockConfigurationLogger;
        Configuration::setLogger($mockLogger);
        $this->assertAttributeEquals($mockLogger, '_logger', '\Bamboo\Log');
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

        $clientListeners = $client->getEventDispatcher()->getListeners();

        // Check that the cache plugin was properly attached
        $this->assertInstanceOf('Guzzle\Plugin\Cache\CachePlugin', $clientListeners['request.sent'][0][0]);
    }
}

class MockConfigurationLogger {
    public function log() {}
}
