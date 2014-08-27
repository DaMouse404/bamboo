<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Schedules;

class SchedulesTest extends BambooTestCase
{
    private $_broadcasts;

    public function setup() {
        parent::setupRequest("schedule@channel_bbcone_schedule");
        $feedObject = new Schedules(array(), 'bbc_one_london', '2014-08-13');
        $this->_broadcasts = $feedObject->getBroadcasts();
    }

    public function testBroadcastItemType() {
        $this->assertEquals(
            get_class($this->_broadcasts[0]),
            "Bamboo\Models\Broadcast"
        );
    }

    // TODO: Update fixturator to allow trimming to X items
    //public function testCountBroadcasts() {
    //    $this->assertCount(2, $this->_broadcasts);
    //}

}

