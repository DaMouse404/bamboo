<?php

namespace Bamboo\Tests;

/**
 * Base testcase class for all Bamboo testcases.
 */
abstract class BambooBaseTestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown() {
        parent::tearDown();
    }

    public function setup() {
        parent::setup();
    }

}
