<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Episodes;

class EpisodesTest extends BambooClientTestCase
{
    private $_episodes = array();

    public function setUp() {
        parent::setUp();
        \Bamboo\Configuration::addFakeRequest('episodes', 'episodes_proms');
        $feedObject = new Episodes(array(), 'gggggggg');
        $this->_episodes = $feedObject->getElements();
    }

    public function testEpisodeItemType() {
        $this->assertEquals(
                get_class($this->_episodes[0]),
                "Bamboo\Models\Episode"
        );
    }

    public function testEpisodeItemCount() {
        $this->assertCount(1, $this->_episodes);
    }

    public function testMultiPid() {
        $feedObject = new Episodes(array(), array('a','b','c'));

        $this->assertAttributeEquals(
            array(
                'episodes/a',
                'episodes/b',
                'episodes/c'
            ),
            '_feeds',
            $feedObject
        );
    }

    public function testSinglePidArray() {
        $feedObject = new Episodes(array(), array('12345678'));

        $this->assertAttributeEquals(array('episodes/12345678'), '_feeds', $feedObject);
    }

    public function testSinglePid() {
        $feedObject = new Episodes(array(),'87654321');

        $this->assertAttributeEquals(array('episodes/87654321'), '_feeds', $feedObject);
    }

    public function testGetRawEpisodes() {
        \Bamboo\Configuration::addFakeRequest('episodes', 'episodes_proms');
        $feedObject = new Episodes(array(), '12345678');
        $rawEpisodes = $feedObject->getRawEpisodes();
        $this->assertEquals($rawEpisodes[0]->id, 'p014mxpr');
    }
}
