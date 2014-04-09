<?php

namespace Bamboo\Tests;

use \Bamboo\Feeds\Client;
use \Bamboo\Feeds\Http\Fake;

/**
 * Base testcase class for all Bamboo testcases.
 */
abstract class BambooTestCase extends \PHPUnit_Framework_TestCase
{
    protected function setupRequest($feed) {
    	$_GET['_fake'] = $feed;
    	$httpFake = new Fake();
    	$path =  dirname(__FILE__) . '/../../../tests/fixtures/';
    	$httpFake->setFixturesPath($path);
        Client::getInstance()->setFakeHttpClient(
            $httpFake
        );
    }
}