<?php

namespace Bamboo\Tests\Feeds\Groups;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Groups\Episodes;

class EpisodesTest extends BambooClientTestCase
{

  private $_feedObject;

  public function setUp() {
    parent::setupRequest("episodes@groups_episodes");
    $this->_feedObject = new Episodes(array(), 'p00zw1jd');
  }

  public function testGetEpisodes() {
    $elements = $this->_feedObject->getEpisodes();
    $this->assertTrue(is_array($elements));
    $this->assertInstanceOf(
        "Bamboo\Models\Episode",
        $elements[0]
    );
  }

  public function testGetGroup() {
    $this->assertInstanceOf(
        "Bamboo\Models\Group",
        $this->_feedObject->getGroup()
    );
  }

  public function testGetTotalCount() {
    $this->assertEquals(
        $this->_feedObject->getTotalCount(),
        20
    );

  }

}
