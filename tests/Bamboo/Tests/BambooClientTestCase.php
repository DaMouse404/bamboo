<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\Configuration;
use \Bamboo\Http\Fake;
use \Bamboo\Http\Fail;
use \Bamboo\Tests\BambooBaseTestCase;

/**
 * Base testcase class for all Bamboo testcases.
 */
abstract class BambooClientTestCase extends BambooBaseTestCase
{
    const FIXTURE_PATH =  '/../../../tests/fixtures/';

    /*
     * Generically clean up after setupRequests
     */
    public function tearDown() {
        unset($_GET['_fake']);
        Configuration::setFakeHttpClient(null);
        Client::getInstance()->setServiceProxy(false);

        parent::tearDown();
    }

    /*
     * By default we will use fixtures..use fake client.
     */
    protected function setupRequest($feed, $httpFake=false) {
        $_GET['_fake'] = $feed;
        if (!$httpFake) {
            $httpFake = new Fake();
        }
        $path =  dirname(__FILE__) . self::FIXTURE_PATH;
        $httpFake->setFixturesPath($path);
        \Bamboo\Counter::setCounter("Bamboo\CounterFake");

        Configuration::setFakeHttpClient($httpFake);

        Client::getInstance()->setServiceProxy(true);
    }

    protected function setupParallelRequest($feed, $fakeClient=false) {
        return $this->setupRequest($feed[0][0], $fakeClient);
    }

    protected function setupFailRequest($feed, $errorClass = null, $statusCode = "") {

        // As deals with errors, set fake Counter
        \Bamboo\Counter::setCounter("Bamboo\CounterFake");
        $_GET['_fail'] = $feed;

        $httpFail = new Fail();
        if ($errorClass) {
            $httpFail->setErrorClass($errorClass);
        }
        if ($statusCode) {
            $httpFail->setStatusCode($statusCode);
        }
        $path =  dirname(__FILE__) . self::FIXTURE_PATH;
        $httpFail->setFixturesPath($path);

        Configuration::setFailHttpClient($httpFail);

        Client::getInstance()->setServiceProxy(true);
    }
}
