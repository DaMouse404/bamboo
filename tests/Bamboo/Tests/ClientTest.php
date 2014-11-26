<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\CounterFake;
use Bamboo\Feeds\Atoz;

/*
 * Using ATOZ as the example feed testing Clients responsibilities.
 */
class ClientTest extends BambooClientTestCase
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
        \Bamboo\Configuration::addFakeRequest('atoz', 'atoz_a_programmes');
        $client = Client::getInstance()->getClient("atoz");

        $this->assertInstanceOf('Bamboo\Http\Fake', $client);
    }

    public function testGetHttpClientFail() {
        parent::setupFailRequest('atoz', 'atoz_a_programmes');
        $client = Client::getInstance()->getClient("atoz/a/programmes");

        $this->assertInstanceOf('Bamboo\Http\Fail', $client);
    }

    public function testSetLang() {
        \Bamboo\Configuration::addFakeRequest('atoz', 'atoz_a_programmes');

        $defaultLang = Client::getInstance()->getParam('lang');
        Client::getInstance()->setLang('cy');
        $newLang = Client::getInstance()->getParam('lang');

        $this->assertEquals('en', $defaultLang);
        $this->assertEquals('cy', $newLang);
    }

    public function testConfigSetters() {
        $client = Client::getInstance();

        $client->setServiceProxy('Proxy Cake');
        $this->assertAttributeEquals('Proxy Cake', '_serviceProxy', $client);
    }

    /*
     * Test Post Fetch
     * - Correct response parsing
     *
     * Very few testing this as more in FEED tests
     */
    public function testParsesResponse() {
        \Bamboo\Configuration::addFakeRequest('atoz', 'atoz_a_programmes');
        $feedObject = new Atoz(array(), 'a');

        $this->assertInstanceOf('Bamboo\Feeds\Atoz', $feedObject);
    }

    /*
     * Test Post Fetch
     * - Test Translate response Exception
     */
    public function testServerError() {
        parent::setupFailRequest('atoz', 'atoz_a_programmes');
        $this->setExpectedException('Bamboo\Exception\ServerError');
        $feedObject = new Atoz(array(), 'a');
    }

    public function testBadRequest() {
        parent::setupFailRequest(
            'atoz',
            'atoz_a_programmes',
            'Guzzle\Http\Exception\ClientErrorResponseException',
            400
        );

        $this->setExpectedException('Bamboo\Exception\BadRequest');

        $feedObject = new Atoz(array(), 'a');
    }

    public function testNotFound() {
        parent::setupFailRequest(
            'atoz',
            'atoz_a_programmes',
            'Guzzle\Http\Exception\ClientErrorResponseException',
            404
        );

        $this->setExpectedException('Bamboo\Exception\NotFound');

        $feedObject = new Atoz(array(), 'a');
    }

    public function testRequestAll() {
        $requests = array(
            array('atoz', array()),
            array('atoz', array()),
            array('atoz', array())
        );
        $client = Client::getInstance();
        \Bamboo\Configuration::addFakeRequest('atoz', 'atoz_a_programmes');
        $responses = $client->requestAll($requests);

        $this->assertEquals(3,count($responses));
    }

    public function testCurlExceptionsInRequestAll() {
        $this->_requestAllExceptionTest(
            new \Guzzle\Http\Exception\CurlException('Timed Out'),
            'Bamboo\Exception\CurlError',
            'BAMBOO_PROXY_CURLERROR'
        );
    }

    public function testNormalExceptionsInRequestAll() {
        $this->_requestAllExceptionTest(
            new \Exception('Parse Error'),
            'Exception',
            'BAMBOO_PROXY_OTHER'
        );
    }

    private function _requestAllExceptionTest($errorIn, $errorExpected, $counter) {
        $requests = array(
            array('atoz', array()),
            array('atoz', array()),
            array('atoz', array())
        );

        $client = $this->_multiRequestException($requests, 'atoz_a_programmes', $errorIn);

        $this->setExpectedException($errorExpected);
        try {
            $responses = $client->requestAll($requests);
        } catch (\Exception $e) {
            $this->assertEquals(1, CounterFake::getCount($counter));
            CounterFake::resetCount($counter);
            throw $e;
        }
    }


    /**
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
                'atoz',
                'atoz_a_programmes',
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

    private function _multiRequestException($requests, $fixture, $err) {
        $stub = $this->getMock('Bamboo\Http\Fail', array('send'));
        $stub->method('send')->will($this->returnCallback(function ($requests) use ($err) {
            $multi = new \Guzzle\Http\Exception\MultiTransferException();
            $multi->setExceptions(array($err));

            throw $multi;
        }));

        $client = Client::getInstance();

        \Bamboo\Configuration::setFailHttpClient($stub);
        \Bamboo\Configuration::addFailRequest($requests[0][0], $fixture);

        Client::getInstance()->setServiceProxy(true);

        return $client;
    }

    private function _counterTest($fixture, $counter, $wrongCounter) {
        try {
            CounterFake::resetCount($counter);
            CounterFake::resetCount($wrongCounter);
            $startCount = CounterFake::getCount($counter);
            parent::setupFailRequest('atoz', $fixture);
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
