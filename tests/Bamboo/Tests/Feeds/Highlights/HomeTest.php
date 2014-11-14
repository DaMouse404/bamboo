<?php

namespace Bamboo\Tests\Feeds\Highlights;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Highlights\Home;

class HomeTest extends BambooClientTestCase
{

  private $_elements;

  public function setUp() {
    parent::setup();
    \Bamboo\Configuration::addFakeRequest('highlights', 'home_highlights');
    $feedObject = new Home();
    $this->_elements = $feedObject->getElements();
  }

  public function testGetElements() {
    $this->assertTrue(is_array($this->_elements));
  }

  // Fixturator trimTo required
  //public function testFeedSize() {
  //  $this->assertEquals(
  //     sizeof($this->_elements),
  //      21
  //  );
  //}

  public function testFeedItemType() {
    $this->assertEquals(
        get_class($this->_elements[0]),
        "Bamboo\Models\Episode"
    );
  }

}
