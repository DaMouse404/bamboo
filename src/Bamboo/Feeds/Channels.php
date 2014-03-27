<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Channel;

class Channels extends Base {

    protected $_feed = 'channels';
    protected $_response;

    /*
     * Return array of Channel models
     */
    public function getChannels() {
    	$channels = array();
    	foreach ($this->_response->channels as $channel) {
    		$channels[] = new Channel($channel);
    	}

    	return $channels;
    }

}