<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Channels;

class ChannelsTest extends BambooTestCase {

    private $_channels = array();

    public function setUp() {
        $channelModel = new Channels();

        $this->_channels = $channelModel->getChannels();
    }

    public function testChannelCount() {
        $this->assertCount(10, $this->_channels);
    }

    public function testChannelClass() {
        $this->assertInstanceOf('Bamboo\Models\Channel', $this->_channels[0]);
    }

    public function testPartnerChannels() {
        $feedObject = new Channels(array(), true);

        $this->assertAttributeEquals('channels/partner', '_feed', $feedObject);
    }
}
