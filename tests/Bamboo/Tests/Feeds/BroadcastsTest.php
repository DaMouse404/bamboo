<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Broadcasts;

class BroadcastsTest extends BambooTestCase
{

    public function setUp() {
        parent::setupRequest("broadcasts@channel_bbcone_broadcasts");
    }

    public function testBroadcastItemType() {
        $feedObject = new Broadcasts(array(), 'bbc_one_london');
        $channels = $feedObject->getBroadcasts();
        $this->assertCount(20, $channels['bbc_one_london']);
        $this->assertInstanceOf(
            "Bamboo\Models\Broadcast",
            $channels['bbc_one_london'][0]
        );
    }

    public function testMultiFeed() {
        $feedObject = new Broadcasts(array(), array('bbc_cake', 'bbc_foo'));

        $this->assertAttributeEquals(
            array(
                '/channels/bbc_cake/broadcasts',
                '/channels/bbc_foo/broadcasts'
            ),
            '_feeds',
            $feedObject
        );
    }
}
