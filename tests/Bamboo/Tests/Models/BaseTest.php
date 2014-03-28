<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Base;

class BaseTest extends BambooTestCase {
  private $_base;

  public function setup() {
    $object = $this->_mockBaseObject();
    $this->_base = new Base($object);
  }

  public function testGetId() {
    $this->assertEquals($this->_base->getId(), 12);
  }

  public function testGetTitle() {
    $this->assertEquals($this->_base->getTitle(), 'James Bond Movie 1');
  }

  private function _mockBaseObject() {
    $object = (object) array(
      'id' => 12, 
      'title' => 'James Bond Movie 1'
    );

    return $object;
  }
}