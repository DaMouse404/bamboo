<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Schedules;
use Bamboo\Models\Broadcast;

class SchedulesTest extends BambooTestCase
{
    private $_broadcasts;

    public function testBroadcastItemType() {
        $broadcasts = $this->_getBroadcastsForFixture('channel_bbcone_schedule');
        $this->assertEquals(
            "Bamboo\Models\Broadcast",
            get_class($broadcasts[0])
        );
    }

    public function testDuplicatedBroadcasts() {
        //These broadcasts are all OK
        $broadcasts = $this->_getBroadcastsForFixture('schedule_overlaps_correct');
        $this->assertCount(8, $broadcasts);

        //Second and third are duplicated
        $broadcasts = $this->_getBroadcastsForFixture('schedule_overlaps_duplicated');
        $this->assertEquals(null, $broadcasts[1]->getEpisode()->getId());
        $this->assertCount(7, $broadcasts);

        //Second and third are overlapped
        $broadcasts = $this->_getBroadcastsForFixture('schedule_overlaps_duplicated');
        $this->assertEquals(null, $broadcasts[1]->getEpisode()->getId());
        $this->assertCount(7, $broadcasts);
        $this->_assertStartEndTimes($broadcasts[1], '07:00', '09:00');

        //Third is inside second
        $broadcasts = $this->_getBroadcastsForFixture('schedule_overlaps_inside');
        $this->assertEquals(null, $broadcasts[1]->getEpisode()->getId());
        $this->_assertStartEndTimes($broadcasts[1], '07:00', '09:00');
        $this->assertCount(7, $broadcasts);


        //There's a gap between second and third
        $broadcasts = $this->_getBroadcastsForFixture('schedule_overlaps_gap');
        $this->assertEquals(null, $broadcasts[2]->getEpisode()->getId());
        $this->_assertStartEndTimes($broadcasts[2], '08:00', '08:15');

        $this->assertNotEquals(null, $broadcasts[3]->getEpisode()->getId());
        $this->_assertStartEndTimes($broadcasts[3], '08:15', '09:00');
        $this->assertCount(9, $broadcasts);
    }

    private function _assertStartEndTimes($broadcast, $start, $end) {
        $today = date('Y-m-d');
        $this->assertEquals($today . 'T' . $start . ':00.000Z', $broadcast->getStartTime());
        $this->assertEquals($today . 'T' . $end . ':00.000Z', $broadcast->getEndTime());
    }

    private function _getBroadcastsForFixture($fixture) {
        parent::setupRequest("schedule@" . $fixture);
        $feedObject = new Schedules(array(), 'bbc_one_london', '2014-08-13');
        return $feedObject->getBroadcasts();
    }

}

