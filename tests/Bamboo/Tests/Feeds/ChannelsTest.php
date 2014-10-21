<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Channels;

class ChannelsTest extends BambooTestCase {

    private $_channels = array();

    public function setUp() {
        parent::setupRequest("channels@channels");

        $channelModel = new Channels();

        $this->_channels = $channelModel->getChannels();
    }

    public function testChannelClass() {
        $this->assertInstanceOf('Bamboo\Models\Channel', $this->_channels[0]);
    }

    public function testPartnerChannels() {
        $feedObject = new Channels(array(), true);

        $this->assertAttributeEquals('channels/partner', '_feed', $feedObject);
    }
}
