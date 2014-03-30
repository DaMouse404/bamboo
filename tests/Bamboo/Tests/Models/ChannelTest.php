<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Channel;

class ChannelTest extends BambooTestCase {
  private $_channel;

  public function setup() {
    $object = $this->_mockChannelObject();
    $this->_channel = new Channel($object);
  }

  public function testGetType() {
    $this->assertEquals(
      $this->_channel->getType(),
      'channel'
    );
  }

  public function testGetUnregionalisedID() {
    $this->assertEquals(
      $this->_channel->getUnregionalisedID(),
      'bbc_one'
    );
  }

  public function testGetSlug() {
    $this->assertEquals(
      $this->_channel->getSlug(),
      'bbcone'
    );
  }

  public function testIsChildrens() {
    $this->assertFalse(
      $this->_channel->isChildrens()
    );
  }

  private function _mockChannelObject() {
    $object = (object) array(
        "id" => "bbc_one_london",
        "title" => "BBC One",
        "type" => "channel"
    );

    return $object;
  }
}