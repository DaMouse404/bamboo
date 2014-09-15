<?php

namespace Bamboo\Tests;

use Bamboo\Log;

class LogTest extends BambooTestCase {

    public function tearDown() {
        Log::setLogger(null);
    }

    public function testLoggerSetup() {
        $logger = new MockLogger;
        Log::setLogger($logger);

        $this->assertInstanceOf('Bamboo\Tests\MockLogger', Log::getLogger());
    }

    public function testErr() {
        $this->_setupLogLevel('Cake', Log::ERR);
        Log::err('Cake');
    }

    public function testWarn() {
        $this->_setupLogLevel('Cake', Log::WARN);
        Log::warn('Cake');
    }

    public function testNotice() {
        $this->_setupLogLevel('Cake', Log::NOTICE);
        Log::notice('Cake');
    }

    public function testInfo() {
        $this->_setupLogLevel('Cake', Log::INFO);
        Log::info('Cake');
    }

    public function testDebug() {
        $this->_setupLogLevel('Cake', Log::DEBUG);
        Log::debug('Cake');
    }


    private function _setupLogLevel($msg, $level) {
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
