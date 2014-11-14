<?php

namespace Bamboo\Tests\Feeds\Episodes;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Episodes\Recommendations;

class ProgrammesTest extends BambooClientTestCase
{

  private $_feedObject;

  public function setUp() {
    parent::setup();
    \Bamboo\Configuration::addFakeRequest('recommendations', 'episodes_recommendations');
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
