<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Schedules;

class SchedulesTest extends BambooTestCase
{
    private $_broadcasts;

    public function testBroadcastItemType() {
        $broadcasts = $this->_getBroadcastsForFixture('channel_bbcone_schedule');
        $this->assertEquals(
            get_class($broadcasts[0]), 
            "Bamboo\Models\Broadcast"
        );
    }

    // TODO: Update fixturator to allow trimming to X items
    //public function testCountBroadcasts() {
    //    $this->assertCount(2, $this->_broadcasts);
    //}

    public function testCountBroadcasts() {
        $broadcasts = $this->_getBroadcastsForFixture('channel_bbcone_schedule');
        $this->assertCount(2, $broadcasts);
    }

    public function testOverlaps() {
        $broadcasts = $this->_getBroadcastsForFixture('channel_bbcone_schedule_overlaps');
        $this->assertEquals(
            $broadcasts[0]->getEndTime(),
            $broadcasts[1]->getStartTime()
        );
        $this->assertEquals(
            $broadcasts[1]->getEndTime(),
            $broadcasts[2]->getStartTime()
        );
    }

    public function testDuplicates() {
        $broadcasts = $this->_getBroadcastsForFixture('channel_bbcone_schedule_overlaps');
        $this->assertCount(3, $broadcasts);
    }

    private function _getBroadcastsForFixture($fixture) {
        parent::setupRequest("schedule@" . $fixture);
        $feedObject = new Schedules(array(), 'bbc_one_london', '2014-08-13');
        return $feedObject->getBroadcasts();
    }

}

