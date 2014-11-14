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
        Configuration::setFakeHttpClient(null);
        Configuration::clearFakeRequests();
        Configuration::clearFailRequests();
        Client::getInstance()->setServiceProxy(false);

        parent::tearDown();
    }

    public function setup() {
        $path =  dirname(__FILE__) . self::FIXTURE_PATH;

        $httpFake = new Fake();
        $httpFake->setFixturesPath($path);
        \Bamboo\Counter::setCounter("Bamboo\CounterFake");

        Configuration::setFakeHttpClient($httpFake);

        Client::getInstance()->setServiceProxy(true);
        parent::setup();
    }

    protected function setupFailRequest($feed, $fixture, $errorClass = null, $statusCode = "") {
        // As deals with errors, set fake Counter
        \Bamboo\Counter::setCounter("Bamboo\CounterFake");

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

        Configuration::addFailRequest($feed, $fixture);

        Client::getInstance()->setServiceProxy(true);
    }
}
