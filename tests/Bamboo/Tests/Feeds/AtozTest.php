<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Atoz;

class AtozTest extends BambooTestCase
{

  public function setup() {
    parent::setupRequest("atoz_a_programmes");
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