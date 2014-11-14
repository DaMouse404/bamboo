<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Atoz;

class AtozTest extends BambooClientTestCase
{

  public function setUp() {
    parent::setup();
    \Bamboo\Configuration::addFakeRequest('atoz', 'atoz_a_programmes');
    $feedObject = new Atoz(array(), 'a');
    $this->_programmes = $feedObject->getElements();
  }

  public function testProgrammeItemType() {
    $this->assertEquals(
        get_class($this->_programmes[0]),
        "Bamboo\Models\Programme"
    );
  }

}
