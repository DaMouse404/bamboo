<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooClientTestCase;

class CompilationsTest extends BambooClientTestCase {

    private $groups = array();

    public function setUp() {
        \Bamboo\Configuration::addFakeRequest('compilations', 'compilations');
        parent::setup();
    }

    public function testCompilation() {
        $feed = new \Bamboo\Feeds\Compilations('matt');

        $compilation = $feed->getCompilation();
        $this->assertInstanceOf('Bamboo\Models\Compilation', $compilation);
    }

    public function testGetGroups() {
        $feed = new \Bamboo\Feeds\Compilations('matt');

        $groups = $feed->getGroups();

        $this->assertEquals(1, $feed->getGroupCount());
        $this->assertEquals(1, $feed->getTotalGroupCount());
        $this->assertInstanceOf('Bamboo\Models\Group', $groups[0]);
    }
}
