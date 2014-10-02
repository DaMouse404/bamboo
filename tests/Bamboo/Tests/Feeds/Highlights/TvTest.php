<?php

namespace Bamboo\Tests\Feeds\Highlights;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Highlights\Tv;

class TvTest extends BambooTestCase
{

  private $_elements;

  public function setUp() {
    parent::setupRequest("highlights@tv_highlights");
    $feedObject = new Tv();
    $this->_elements = $feedObject->getElements();
  }

  public function testGetElements() {
    $this->assertInternalType("array", $this->_elements);
  }

  public function testFeedItemType() {
    $this->assertInstanceOf(
        "Bamboo\Models\Promotion",
        $this->_elements[0]
    );
  }

}
