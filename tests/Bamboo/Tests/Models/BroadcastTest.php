<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Broadcast;

class BroadcastTest extends BambooBaseTestCase
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

    public function testIsSimulcast() {
        $params = array(
            'blanked' => false
        );
        // Not blanked and on now
        $broadcast = $this->_createTimedBroadcast('-1 hours', '+1 hours', $params);
        $this->assertTrue($broadcast->isSimulcast());

        // Not blanked and on in the future
        $broadcast = $this->_createTimedBroadcast('+1 hours', '+2 hours', $params);
        $this->assertFalse($broadcast->isSimulcast());

        // Blanked and on now
        $params['blanked'] = true;
        $broadcast = $this->_createTimedBroadcast('-1 hours', '+1 hours', $params);
        $this->assertFalse($broadcast->isSimulcast());

        // Blanked and on in the future
        $broadcast = $this->_createTimedBroadcast('+1 hours', '+2 hours', $params);
        $this->assertFalse($broadcast->isSimulcast());
    }

    public function testIsCatchup() {
        $episode = $this->_createEpisode('available');
        $broadcast = $this->_createBroadcast($episode);
        $this->assertTrue($broadcast->isCatchUp());

        $episode = $this->_createEpisode('unavailable');
        $broadcast = $this->_createBroadcast($episode);
        $this->assertFalse($broadcast->isCatchUp());
    }

    public function testIsAvailableToWatch() {
        $episode = $this->_createEpisode('available');
        $broadcast = $this->_createTimedBroadcast('-1 hours', '+1 hours', $episode);
        $this->assertTrue($broadcast->isAvailableToWatch());

        $episode = $this->_createEpisode('unavailable');
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours', $episode);
        $this->assertFalse($broadcast->isAvailableToWatch());
    }

    public function testIsComingSoon() {
        $params = $this->_createEpisode('coming_soon');
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours', $params);
        $this->assertTrue($broadcast->isComingSoon());

        $params = $this->_createEpisode('unavailable');
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours', $params);
        $this->assertFalse($broadcast->isComingSoon());
    }

    public function testIsOnNow() {
        $broadcast = $this->_createTimedBroadcast('-1 hours', '+1 hours');

        $this->assertTrue($broadcast->isOnNow());
    }

    public function testIsNotOnNow() {
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours');

        $this->assertFalse($broadcast->isOnNow());
    }

    public function testIsOnNext() {
        $broadcast = $this->_createTimedBroadcast('+5 minutes', '+1 hour');

        $this->assertTrue($broadcast->isOnNext());
    }

    public function testIsNotOnNext() {
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours');

        $this->assertFalse($broadcast->isOnNext());
    }

    private function _createEpisode($status = 'available') {
        return array(
            'episode' => (object) array(
                'title' => 'Episode Title',
                'status' => $status
            )
        );
    }

    private function _createBroadcast($params) {
        return new Broadcast((object) $params);
    }

    private function _createTimedBroadcast($startOffset, $endOffset, $opts = array()) {
        $startTime = new \DateTime();
        $endTime = new \DateTime();

        $params = array(
            'scheduled_start' => $startTime->modify($startOffset)->format($this->_timeFormat),
            'scheduled_end' => $endTime->modify($endOffset)->format($this->_timeFormat)
        );

        if(!empty($opts)) {
            $params = array_merge($params, $opts);
        }

        return $this->_createBroadcast($params);
    }
}
