<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\CounterFake;
use Bamboo\Feeds\Atoz;

class ClientTest extends BambooTestCase
{

    public function testSetLang() {
        parent::setupRequest('atoz_a_programmes');

        $defaultLang = Client::getInstance()->getParam('lang');
        Client::getInstance()->setLang('cy');
        $newLang = Client::getInstance()->getParam('lang');
        
        $this->assertEquals('en', $defaultLang);
        $this->assertEquals('cy', $newLang);
    }

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

    public function testServerErrorApigeeCounter() { 
        $this->_counterTest(
            'apigee_failure', 
            'BAMBOO_APIGEE_SERVERERROR',
            'BAMBOO_IBL_SERVERERROR'
        );
    }

    public function testServerErrorIblCounter() {
        $this->_counterTest(
            'ibl_failure', 
            'BAMBOO_IBL_SERVERERROR',
            'BAMBOO_APIGEE_SERVERERROR'
        );
    }

    public function testEmptyResponseApigeeCounter() { 
        $this->_counterTest(
            'empty_response', 
            'BAMBOO_APIGEE_SERVERERROR',
            'BAMBOO_IBL_SERVERERROR'
        );
    }

    public function testUnknownResponseApigeeCounter() { 
        $this->_counterTest(
            'unknown_json_response', 
            'BAMBOO_APIGEE_SERVERERROR',
            'BAMBOO_IBL_SERVERERROR'
        );
    }

    public function testBadRequestApigeeCounter() {   
        try {
            CounterFake::resetCount('BAMBOO_APIGEE_BADREQUEST');
            $startCount = CounterFake::getCount('BAMBOO_APIGEE_BADREQUEST');
            parent::setupFailRequest(
                'atoz@atoz_a_programmes', 
                'Guzzle\Http\Exception\ClientErrorResponseException', 
                400
            );
            $feedObject = new Atoz(array(), 'a');
        } catch (\Bamboo\Exception $e) {
            $endCount = CounterFake::getCount('BAMBOO_APIGEE_BADREQUEST');

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