<?php

namespace Bamboo\Tests;

use Bamboo\Log;

class LogTest extends BambooBaseTestCase {

    public function tearDown() {
        Log::setLogger(null);
    }

    public function testLoggerSetup() {
        $logger = new MockLogger;
        Log::setLogger($logger);

        $this->assertInstanceOf('Bamboo\Tests\MockLogger', Log::getLogger());
    }

    public function testErr() {
        $this->_setupLogLevelTest('Cake', Log::ERR);
        Log::err('Cake');
    }

    public function testWarn() {
        $this->_setupLogLevelTest('Cake', Log::WARN);
        Log::warn('Cake');
    }

    public function testNotice() {
        $this->_setupLogLevelTest('Cake', Log::NOTICE);
        Log::notice('Cake');
    }

    public function testInfo() {
        $this->_setupLogLevelTest('Cake', Log::INFO);
        Log::info('Cake');
    }

    public function testDebug() {
        $this->_setupLogLevelTest('Cake', Log::DEBUG);
        Log::debug('Cake');
    }


    private function _setupLogLevelTest($msg, $level) {
        $mock = $this->getMock('Bamboo\Tests\MockLogger', array('log'));

        $mock->expects($this->once())
             ->method('log')
             ->with($this->equalTo($msg), $this->equalTo($level));

        Log::setLogger($mock);
    }
}

class MockLogger {
    public function log() {}
}
