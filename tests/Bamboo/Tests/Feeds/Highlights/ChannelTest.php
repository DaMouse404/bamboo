<?php

namespace Bamboo\Tests\Feeds\Highlights;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Highlights\Channel;

class ChannelTest extends BambooClientTestCase
{

  private $_elements;
  private $_channel;

  public function setUp() {
    parent::setup();
    \Bamboo\Configuration::addFakeRequest('highlights', 'channel_highlights');
    $feedObject = new Channel(array(), 'bbc_one_london');
    $this->_elements = $feedObject->getElements();
    $this->_channel = $feedObject->getChannel();
  }

  public function testGetElements() {
    $this->assertInternalType('array', $this->_elements);
  }

  public function testGetChannel() {
    $this->assertInstanceOf(
        "Bamboo\Models\Channel",
        $this->_channel
    );
  }

}
