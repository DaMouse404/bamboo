<?php

namespace Bamboo\Tests;

use \Bamboo\Counter;
use \Bamboo\CounterFake;


class CounterTest extends BambooBaseTestCase
{
    public function testCounterGetterAndSetter() {
        $counter = new MockCounter;
        Counter::setCounter($counter);

        $this->assertInstanceOf('Bamboo\Tests\MockCounter', Counter::getCounter());
    }

    public function testCounter() {
        $counterName = 'BAMBOO_IBL_SERVERERROR';
        $counter = new CounterFake($counterName);
        Counter::setCounter('Bamboo\CounterFake');
        CounterFake::resetCount($counterName);
        Counter::increment($counterName);

        $this->assertEquals(1, CounterFake::getCount($counterName));
    }
}

class MockCounter {}
