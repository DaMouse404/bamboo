<?php

namespace Bamboo\Tests;

/**
 * Base testcase class for all Bamboo testcases.
 */
abstract class BambooTestCase extends \PHPUnit_Framework_TestCase
{
	public function setFakeClient() {
        Bamboo\Feeds\Client::getInstance()->setHttpClient(
            new Bamboo\Feeds\HttpClient
        );
	}
}