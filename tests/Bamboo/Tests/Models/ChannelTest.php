<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Channel;

class ChannelTest extends BambooBaseTestCase
{

  private $_channel;

  public function setUp() {
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
    $newChannel = $this->_mockChannelObject();
    $newChannel->id = 'cbbc';
    $newChannel = new Channel($newChannel);
    $this->assertEquals(
        $newChannel->getUnregionalisedID(),
        'cbbc'
    );
  }

  public function testGetSlug() {
    $this->assertEquals(
        $this->_channel->getSlug(),
        'bbcone'
    );
  }

  public function testGetRegion() {
    $this->assertEquals(
        $this->_channel->getRegion(),
        'LONDON'
    );
  }

  public function testIsChildrens() {
    $this->assertFalse(
        $this->_channel->isChildrens()
    );
  }

  public function testHasSchedule() {
      $channelData = $this->_mockChannelObject(
                    array("has_schedule"=>true)
                 );
      $channel = new Channel($channelData);
      $this->assertTrue($channel->hasSchedule());

      $channelData = $this->_mockChannelObject(
                    array("has_schedule"=>false)
                );
      $channel = new Channel($channelData);
      $this->assertFalse($channel->hasSchedule());

      $this->assertFalse($this->_channel->hasSchedule());
  }

  private function _mockChannelObject($override = array()) {
    $object = (object) array_merge(
        array(
            "id" => "bbc_one_london",
            "title" => "BBC One",
            "type" => "channel",
            "region" => "LONDON"
        ),
        $override
    );

    return $object;
  }
}
