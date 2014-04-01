<?php

namespace Bamboo\Tests;

use \Bamboo\Feeds\Client;
use \Bamboo\Feeds\HttpFake;

/**
 * Base testcase class for all Bamboo testcases.
 */
abstract class BambooTestCase extends \PHPUnit_Framework_TestCase
{
    protected function setup() {
        Client::getInstance()->setHttpClient(
            new HttpFake
        );
    }
}