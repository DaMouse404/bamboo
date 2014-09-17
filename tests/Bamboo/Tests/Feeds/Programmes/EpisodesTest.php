<?php

namespace Bamboo\Tests\Feeds\Programmes;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Programmes\Episodes;

class EpisodesTest extends BambooTestCase
{

  private $_feedObject;

  public function setUp() {
    parent::setupRequest("episodes@programmes_episodes");
    $this->_feedObject = new Episodes(array(), 'b006m86d');
  }

  public function testGetEpisodes() {
    $elements = $this->_feedObject->getEpisodes();
    $this->assertInternalType('array', $elements);
    $this->assertInstanceOf(
        "Bamboo\Models\Episode",
        $elements[0]
    );
  }

  public function testGetResponse() {
    $this->assertInternalType('array', $this->_feedObject->getResponse());
  }

  public function testGetTotalCount() {
    $this->assertEquals(
        $this->_feedObject->getTotalCount(),
        20
    );

  }

}
