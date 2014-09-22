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

    public function testIsSimulcast() {
        $params = array(
            'blanked' => false
        );
        // On now and not blanked
        $broadcast = $this->_createTimedBroadcast('-1 hours', '+1 hours', $params);
        $this->assertTrue($broadcast->isSimulcast());
        // Not on now
        $broadcast = $this->_createBroadcast('+1 hours', '+2 hours', $params);
        $this->assertFalse($broadcast->isBlanked());
        // On now and blanked
        $params['blanked'] = true;
        $broadcast = $this->_createBroadcast('-1 hours', '+1 hours', $params);
        $this->assertFalse($broadcast->isBlanked());
    }

    public function testIsCatchup() {
        $params = array(
            'status' => 'available'
        );
        $broadcast = $this->_createBroadcast($params);
        $this->assertTrue($broadcast->isCatchUp());

        $params['status'] = 'unavailable';
        $broadcast = $this->_createBroadcast($params);
        $this->assertFalse($broadcast->isCatchUp());
    }

    public function testIsAvailableToWatch() {
        $params = array(
            'status' => 'available'
        );
        $broadcast = $this->_createTimedBroadcast('-1 hours', '+1 hours', $params);
        $this->assertTrue($broadcast->isAvailableToWatch());

        echo '<pre>';
        print_r($broadcast->getEpisode());
        echo '</pre>';
        
        $params['status'] = 'unavailable';
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours', $params);
        $this->assertFalse($broadcast->isAvailableToWatch());
    }

    public function testIsComingSoon() {
        $params = array(
            'status' => 'coming_soon'
        );
        // On now and not blanked
        $broadcast = $this->_createTimedBroadcast('-2 hours', '-1 hours', $params);
        $this->assertTrue($broadcast->isComingSoon());

        $params['status'] = 'unavailable';
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
