<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Categories;

class StaticFeedTest extends BambooTestCase
{
    public function testNoFeed () {
        $this->setExpectedException('Bamboo\Exception\EmptyFeed');
        $fake = new FakeStaticFeed();
    }

    public function testEmptyFeed () {
        $stub = $this->getMock('EmptyStaticFeed', array('fetchFile'));
        $stub->method('fetchFile')->will($this->returnValue(false));

        $this->setExpectedException('Bamboo\Exception\EmptyFeed');
        $fake = new EmptyStaticFeed();
    }
}

class FakeStaticFeed extends \Bamboo\Feeds\StaticBase {

    protected $_feed = 'non-existant-feed';
    protected $_response;
}

class EmptyStaticFeed extends \Bamboo\Feeds\StaticBase {

    protected $_feed = 'empty-feed';
    protected $_response;
}