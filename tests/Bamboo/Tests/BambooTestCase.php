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
    /*
     * By default we will use fixtures..use fake client.
     */
    protected function setupRequest($feed) {
        $_GET['_fake'] = $feed;
        $httpFake = new Fake();
        $path =  dirname(__FILE__) . '/../../../tests/fixtures/';
        $httpFake->setFixturesPath($path);
        Client::getInstance()->setFakeHttpClient(
            $httpFake
        );
    }

    protected function setupFailRequest($feed, $errorClass = "", $statusCode = "") {

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

        Client::getInstance()->setFailHttpClient(
            $httpFail
        );
            
    }
}