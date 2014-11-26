<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\StaticBase;

class StaticFeedTest extends BambooClientTestCase
{
    public function testFeed () {

        $feedObject = new MockStaticFeed();
        $this->assertAttributeEquals('Cake', '_response', $feedObject);
    }

    public function testNoFeed () {
        $this->setExpectedException('Bamboo\Exception\NotFound');
        $fake = new FakeStaticFeed();
    }

    public function testEmptyFeed () {
        $this->setExpectedException('Bamboo\Exception\EmptyFeed');
        $fake = new EmptyStaticFeed();
    }
}

class MockStaticFeed extends StaticBase {

    protected $_feed = '../../../tests/fixtures/mock_static_feed';
    protected $_response;
}

class FakeStaticFeed extends StaticBase {

    protected $_feed = 'non-existant-feed';
    protected $_response;
}

class EmptyStaticFeed extends StaticBase {

    protected $_feed = '../../../tests/fixtures/empty_response';
    protected $_response;
}