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