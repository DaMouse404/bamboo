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

    /* 
     * Test to ensure name maps to correct constant on error.
     */
    public function testIblServerErrorCounter() {
        try {
            CounterFake::resetCount('BAMBOO_IBL_SERVERERROR');
            $startCount = CounterFake::getCount('BAMBOO_IBL_SERVERERROR');
            parent::setupFailRequest('atoz@ibl_failure');
            $feedObject = new Atoz(array(), 'a');
        } catch (\Bamboo\Exception\ServerError $e) {
            $endCount = CounterFake::getCount('BAMBOO_IBL_SERVERERROR');
            
            $this->assertEquals(0, $startCount);
            $this->assertEquals(1, $endCount);
        }     
    }
    
    public function testApigeeServerErrorCounter() {    
        try {
            CounterFake::resetCount('BAMBOO_APIGEE_SERVERERROR');
            $startCount = CounterFake::getCount('BAMBOO_APIGEE_SERVERERROR');
            parent::setupFailRequest('atoz@apigee_failure');
            $feedObject = new Atoz(array(), 'a');
        } catch (\Bamboo\Exception\ServerError $e) {
            $endCount = CounterFake::getCount('BAMBOO_APIGEE_SERVERERROR');
            
            $this->assertEquals(0, $startCount);
            $this->assertEquals(1, $endCount);
        }   
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

    public function testApigeeBadRequestCounter() {    
        try {
            CounterFake::resetCount('BAMBOO_APIGEE_BADREQUEST');
            $startCount = CounterFake::getCount('BAMBOO_APIGEE_BADREQUEST');
            parent::setupFailRequest(
                'atoz@atoz_a_programmes', 
                'Guzzle\Http\Exception\ClientErrorResponseException', 
                400
            );
            $feedObject = new Atoz(array(), 'a');
        } catch (\Bamboo\Exception\BadRequest $e) {
            $endCount = CounterFake::getCount('BAMBOO_APIGEE_BADREQUEST');

            $this->assertEquals(0, $startCount);
            $this->assertEquals(1, $endCount);
        }   
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

}