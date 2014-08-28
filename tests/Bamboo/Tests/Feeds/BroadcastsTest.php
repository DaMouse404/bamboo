<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Broadcasts;

class BroadcastsTest extends BambooTestCase
{

    private $_broadcasts;

    public function setup() {
        parent::setupRequest("broadcasts@channel_bbcone_broadcasts");
        $feedObject = new Broadcasts(array(), 'bbc_one_london');
        $this->_broadcasts = $feedObject->getBroadcasts();
    }

    public function testBroadcastItemType() {
        $this->assertEquals(
            get_class($this->_broadcasts[0]),
            "Bamboo\Models\Broadcast"
        );
    }

    public function testCountBroadcasts() {
        $this->assertCount(20, $this->_broadcasts);
    }

}
