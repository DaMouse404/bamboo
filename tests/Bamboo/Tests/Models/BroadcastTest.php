<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Broadcast;

class BroadcastTest extends BambooTestCase
{

    protected $_timeFormat = "Y-m-d\TH:i";

    public function testTypeExists() {
        $params = array(
            'type' => 'broadcast'
        );
        $broadcast = $this->_createBroadcast($params);

        $this->assertEquals('broadcast', $broadcast->getType());
    }

    public function testCorrectStartAndEndTimeReturned() {
        $params = array(
            'scheduled_start' => '2014-08-21T12:30:00.000Z',
            'scheduled_end' => '2014-08-21T12:45:00.000Z',
            'transmission_start' => '2014-08-21T12:31:30.000Z',
            'transmission_end' => '2014-08-21T12:43:20.000Z',
        );
        $broadcast = $this->_createBroadcast($params);

        $this->assertEquals('2014-08-21T12:30:00.000Z', $broadcast->getStartTime());
        $this->assertEquals('2014-08-21T12:45:00.000Z', $broadcast->getEndTime());
        $this->assertEquals('2014-08-21T12:31:30.000Z', $broadcast->getStartTime(true));
        $this->assertEquals('2014-08-21T12:43:20.000Z', $broadcast->getEndTime(true));
    }

    public function testRepeat() {
        $params = array(
            'type' => 'broadcast',
            'repeat' => false
        );
        $broadcast = $this->_createBroadcast($params);
        $this->assertFalse($broadcast->isRepeat());

        $params['repeat'] = true;
        $broadcast = $this->_createBroadcast($params);
        $this->assertTrue($broadcast->isRepeat());
    }

    public function testEpisodeType() {
        $params = array(
            'type' => 'broadcast',
            'episode' => (object) array('type'=>'episode', 'title' => 'title', 'subtitle' => 'subtitle')
        );
        $broadcast = $this->_createBroadcast($params);

        $this->assertInstanceOf('Bamboo\Models\Episode', $broadcast->getEpisode());
    }

    public function testIsBlanked() {
        $params = array(
            'blanked' => false
        );
        $broadcast = $this->_createBroadcast($params);
        $this->assertFalse($broadcast->isBlanked());

        $params['blanked'] = true;
        $broadcast = $this->_createBroadcast($params);
        $this->assertTrue($broadcast->isBlanked());
    }

    public function testIsOnNow() {
        $startTime = new \DateTime();
        $endTime = new \DateTime();

        $params = array(
            'scheduled_start' => $startTime->modify('-1 hour')->format($this->_timeFormat),
            'scheduled_end' => $endTime->modify('+1 hour')->format($this->_timeFormat)
        );

        $broadcast = $this->_createBroadcast($params);
        $onNow = $broadcast->isOnNow();

        $this->assertEquals(true, $onNow);
    }

    public function testIsNotOnNow() {
        $startTime = new \DateTime();
        $endTime = new \DateTime();

        $params = array(
            'scheduled_start' => $startTime->modify('-2 hours')->format($this->_timeFormat),
            'scheduled_end' => $endTime->modify('-1 hour')->format($this->_timeFormat)
        );

        $broadcast = $this->_createBroadcast($params);
        $onNow = $broadcast->isOnNow();

        $this->assertEquals(false, $onNow);
    }

    public function testIsOnNext() {
        $startTime = new \DateTime();
        $endTime = new \DateTime();

        $params = array(
            'scheduled_start' => $startTime->modify('+5 minutes')->format($this->_timeFormat),
            'scheduled_end' => $endTime->modify('+1 hour')->format($this->_timeFormat)
        );

        $broadcast = $this->_createBroadcast($params);
        $onNext = $broadcast->isOnNext();

        $this->assertEquals(true, $onNext);
    }

    public function testIsNotOnNext() {
        $startTime = new \DateTime();
        $endTime = new \DateTime();

        $params = array(
            'scheduled_start' => $startTime->modify('-2 hours')->format($this->_timeFormat),
            'scheduled_end' => $endTime->modify('-1 hour')->format($this->_timeFormat)
        );
        $broadcast = $this->_createBroadcast($params);
        $onNext = $broadcast->isOnNext();

        $this->assertEquals(false, $onNext);
    }

    private function _createBroadcast($params) {
        return new Broadcast((object) $params);
    }
}
