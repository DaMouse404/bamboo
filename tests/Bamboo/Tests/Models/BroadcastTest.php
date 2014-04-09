<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Broadcast;

class BroadcastTest extends BambooTestCase
{
    public function testTypeExists() {
        $params = array(
            'type' => 'broadcast'
        );
        $broadcast = $this->_createBroadcast($params);

        $this->assertEquals('broadcast', $broadcast->getType());
    }

    public function testStartEndTimeExists() {
        $params = array(
            'start_time' => '2013-04-09T16:00:00Z',
            'end_time' => '2013-04-09T17:00:00Z'
        );
        $broadcast = $this->_createBroadcast($params);

        $this->assertEquals('2013-04-09T16:00:00Z', $broadcast->getStartTime());
        $this->assertEquals('2013-04-09T17:00:00Z', $broadcast->getEndTime());
    }

    public function testEpisodeType() {
        $params = array(
            'type' => 'broadcast',
            'episode' => (object) array('type'=>'episode', 'title' => 'title', 'subtitle' => 'subtitle')
        );
        $broadcast = $this->_createBroadcast($params);

        $this->assertInstanceOf('Bamboo\Models\Episode', $broadcast->getEpisode());
    }

    private function _createBroadcast($params) {
        return new Broadcast((object) $params);
    }

}
