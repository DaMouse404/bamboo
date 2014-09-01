<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Programme;

class ProgrammeTest extends BambooTestCase
{
    public function testGetEpisodes() {
        $params = array(
            'initial_children' => array(
                (object) array( 'sub_title' => 'Make' ),
                (object) array( 'sub_title' => 'Cake' )
            )
        );

        $prog = $this->_createProgramme($params);

        $episodes = $prog->getEpisodes();

        $this->assertInstanceOf('Bamboo\\Models\\Episode', $episodes[0]);
        $this->assertCount(2, $episodes);
    }

    public function testGetEpisodeCount() {
        $params = array(
            'initial_children' => array(
                (object) array( 'sub_title' => 'Bake' ),
                (object) array( 'sub_title' => 'Cake' )
            )
        );

        $prog = $this->_createProgramme($params);
        $this->assertEquals(2, $prog->getEpisodeCount());
    }

    public function testHasAvailableEpisodes() {
        $params = array(
            'initial_children' => array(
                (object) array( 'sub_title' => 'Fake' ),
                (object) array( 'sub_title' => 'Cake' )
            )
        );

        $prog = $this->_createProgramme($params);
        $this->assertTrue($prog->hasAvailableEpisodes());
    }

    public function testGetLatestAvailable() {
        $params = array(
            'initial_children' => array(
                (object) array( 'sub_title' => 'Take' ),
                (object) array( 'sub_title' => 'Cake' )
            )
        );

        $prog = $this->_createProgramme($params);

        $latest = $prog->getLatestAvailableEpisode();

        $this->assertInstanceOf('Bamboo\\Models\\Episode', $latest);
        $this->assertAttributeEquals('Take', '_sub_title', $latest);
    }

    public function testGetTotalEpisodeCount() {
        $params = array(
            'count' => 404
        );
        $prog = $this->_createProgramme($params);
        $this->assertEquals(404, $prog->getTotalEpisodeCount());
    }

    public function testNoLatestAvailable() {
        $prog = $this->_createProgramme(array());

        $this->assertEmpty($prog->getLatestAvailableEpisode());
    }

    public function _createProgramme($params) {
        return new Programme((object) $params);
    }
}
