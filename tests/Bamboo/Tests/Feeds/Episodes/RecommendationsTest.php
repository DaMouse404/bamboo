<?php

namespace Bamboo\Tests\Feeds\Episodes;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Episodes\Recommendations;

class ProgrammesTest extends BambooTestCase
{

  private $_feedObject;

  public function setUp() {
    parent::setupRequest("recommendations@episodes_recommendations");
    $this->_feedObject = new Recommendations(array(), 'p00y1h7j');
  }

  public function testGetElements() {
    $this->assertInternalType('array', $this->_feedObject->getElements());
  }

  public function testFeedItemType() {
    $elements = $this->_feedObject->getElements();
    $this->assertInstanceOf(
        "Bamboo\Models\Episode",
        $elements[0]
    );
  }

}
