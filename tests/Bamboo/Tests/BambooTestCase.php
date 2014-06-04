<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use \Bamboo\Http\Fake;
use \Bamboo\Http\Fail;

/**
 * Base testcase class for all Bamboo testcases.
 */
abstract class BambooTestCase extends \PHPUnit_Framework_TestCase
{
    const FIXTURE_PATH =  '/../../../tests/fixtures/';

    /*
     * By default we will use fixtures..use fake client.
     */
    protected function setupRequest($feed) {
        $_GET['_fake'] = $feed;

        $httpFake = new Fake();
        $path =  dirname(__FILE__) . self::FIXTURE_PATH;
        $httpFake->setFixturesPath($path);

        Client::getInstance()->setFakeHttpClient(
            $httpFake
        );

        Client::getInstance()->setServiceProxy(true);
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

        Client::getInstance()->setFailHttpClient(
            $httpFail
        );  

        Client::getInstance()->setServiceProxy(true);
    }

}