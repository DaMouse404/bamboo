<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\CounterFake;
use Bamboo\Feeds\Atoz;

/*
 * Using ATOZ as the example feed testing Clients responsibilities.
 */
class ClientTest extends BambooTestCase
{

    /*
     * Test pre fetch
     * - HTTP Clients
     */
    public function testGetHttpClientGuzzle() {
        $client = Client::getInstance()->getClient("atoz");

        $this->assertInstanceOf('Guzzle\Http\Client', $client);
    }

    public function testGetHttpClientFake() {
        parent::setupRequest('atoz@atoz_a_programmes');
        $client = Client::getInstance()->getClient("atoz");

        $this->assertInstanceOf('Bamboo\Http\Fake', $client);
    }

    public function testGetHttpClientFail() {
        parent::setupFailRequest('atoz@atoz_a_programmes');
        $client = Client::getInstance()->getClient("atoz");

        $this->assertInstanceOf('Bamboo\Http\Fail', $client);
    }

    public function testSetLang() {
        parent::setupRequest('atoz_a_programmes');

        $defaultLang = Client::getInstance()->getParam('lang');
        Client::getInstance()->setLang('cy');
        $newLang = Client::getInstance()->getParam('lang');

        $this->assertEquals('en', $defaultLang);
        $this->assertEquals('cy', $newLang);
    }

    /*
     * Test Post Fetch
     * - Correct response parsing
     *
     * Very few testing this as more in FEED tests
     */
    public function testParsesResponse() {
        parent::setupRequest("atoz@atoz_a_programmes");
        $feedObject = new Atoz(array(), 'a');

        $this->assertInstanceOf('Bamboo\Feeds\Atoz', $feedObject);
    }

    /*
     * Test Post Fetch
     * - Test Translate response Exception
     */
    public function testServerError() {
        parent::setupFailRequest('atoz@atoz_a_programmes');
        $this->setExpectedException('Bamboo\Exception\ServerError');
        $feedObject = new Atoz(array(), 'a');
    }

    public function testBadRequest() {
        parent::setupFailRequest(
            'atoz@atoz_a_programmes',
            'Guzzle\Http\Exception\ClientErrorResponseException',
            400
        );

        $this->setExpectedException('Bamboo\Exception\BadRequest');

        $feedObject = new Atoz(array(), 'a');
    }

    public function testNotFound() {
        parent::setupFailRequest(
            'atoz@atoz_a_programmes',
            'Guzzle\Http\Exception\ClientErrorResponseException',
            404
        );

        $this->setExpectedException('Bamboo\Exception\NotFound');

        $feedObject = new Atoz(array(), 'a');
    }

    public function testRequestAll() {
        $requests = array(
            array('atoz@atoz_a_programmes', array()),
            array('atoz@atoz_a_programmes', array()),
            array('atoz@atoz_a_programmes', array())
        );
        $client = Client::getInstance();
        $this->setupParallelRequest($requests);
        $responses = $client->requestAll($requests);

        $this->assertEquals(3,count($responses));
    }

    public function testCurlExceptionsInRequestAll() {
        $stub = $this->getMock('Bamboo\Http\Fake', array('send'));
        $stub->method('send')->will($this->returnCallback(function ($requests) {
            $err = new \Guzzle\Http\Exception\CurlException('Timed Out');
            $multi = new \Guzzle\Http\Exception\MultiTransferException();
            $multi->setExceptions(array($err));
            throw $multi;
        }));

        $requests = array(
            array('atoz@atoz_a_programmes', array()),
            array('atoz@atoz_a_programmes', array()),
            array('atoz@atoz_a_programmes', array())
        );
        $client = Client::getInstance();
        $this->setupParallelRequest($requests, $stub);

        $this->setExpectedException('Bamboo\Exception\CurlError');
        try {
            $responses = $client->requestAll($requests);
        } catch (\Exception $e) {
            $this->assertEquals(1, CounterFake::getCount('BAMBOO_PROXY_CURLERROR'));
            CounterFake::resetCount('BAMBOO_PROXY_CURLERROR');
            throw $e;
        }
    }

    public function testNormalExceptionsInRequestAll() {
        $stub = $this->getMock('Bamboo\Http\Fake', array('send'));
        $stub->method('send')->will($this->returnCallback(function ($requests) {
            $err = new \Exception('Parse Error');
            $multi = new \Guzzle\Http\Exception\MultiTransferException();
            $multi->setExceptions(array($err));
            throw $multi;
        }));

        $requests = array(
            array('atoz@atoz_a_programmes', array()),
            array('atoz@atoz_a_programmes', array()),
            array('atoz@atoz_a_programmes', array())
        );
        $client = Client::getInstance();
        $this->setupParallelRequest($requests, $stub);

        $this->setExpectedException('Exception');
        try {
            $responses = $client->requestAll($requests);
        } catch (\Exception $e) {
            $this->assertEquals(1, CounterFake::getCount('BAMBOO_PROXY_OTHER'));
            CounterFake::resetCount('BAMBOO_PROXY_OTHER');
            throw $e;
        }
    }


    /*
     * Test Post Fetch more
     * - Translate reponse exception+response into Counters
     */
    public function testServerErrorProxyCounter() {
        $this->_counterTest(
            'proxy_failure',
            'BAMBOO_PROXY_SERVERERROR',
            'BAMBOO_IBL_SERVERERROR'
        );
    }

    public function testServerErrorIblCounter() {
        $this->_counterTest(
            'ibl_failure',
            'BAMBOO_IBL_SERVERERROR',
            'BAMBOO_PROXY_SERVERERROR'
        );
    }

    public function testEmptyResponseProxyCounter() {
        $this->_counterTest(
            'empty_response',
            'BAMBOO_PROXY_SERVERERROR',
            'BAMBOO_IBL_SERVERERROR'
        );
    }

    public function testUnknownResponseProxyCounter() {
        $this->_counterTest(
            'unknown_json_response',
            'BAMBOO_PROXY_SERVERERROR',
            'BAMBOO_IBL_SERVERERROR'
        );
    }

    public function testBadRequestProxyCounter() {
        try {
            CounterFake::resetCount('BAMBOO_PROXY_BADREQUEST');
            $startCount = CounterFake::getCount('BAMBOO_PROXY_BADREQUEST');
            parent::setupFailRequest(
                'atoz@atoz_a_programmes',
                'Guzzle\Http\Exception\ClientErrorResponseException',
                400
            );
            $feedObject = new Atoz(array(), 'a');
        } catch (\Bamboo\Exception $e) {
            $endCount = CounterFake::getCount('BAMBOO_PROXY_BADREQUEST');

            $this->assertEquals(0, $startCount);
            $this->assertEquals(1, $endCount);
            $this->assertInstanceOf('\Bamboo\Exception\BadRequest', $e);
        }
    }

    public function testSetCache() {
        $cache = $this->getMockBuilder('Guzzle\Cache\CacheAdapterInterface')
                    ->getMockForAbstractClass();

        Client::getInstance()->setCache($cache);

        $client = Client::getInstance()->getClient('atoz');

        $l = $client->getEventDispatcher()->getListeners();

        // Check that the cache plugin was properly attached
        $this->assertInstanceOf('Guzzle\Plugin\Cache\CachePlugin', $l['request.sent'][0][0]);
    }

    private function _counterTest($fixture, $counter, $wrongCounter) {
        try {
            CounterFake::resetCount($counter);
            CounterFake::resetCount($wrongCounter);
            $startCount = CounterFake::getCount($counter);
            parent::setupFailRequest('atoz@' . $fixture);
            $feedObject = new Atoz(array(), 'a');
        } catch (\Bamboo\Exception\ServerError $e) {
            $endCount = CounterFake::getCount($counter);
            $wrongCounter = CounterFake::getCount($wrongCounter);

            $this->assertEquals(0, $startCount);
            $this->assertEquals(1, $endCount);
            $this->assertEquals(0, $wrongCounter);
        }
    }
}
