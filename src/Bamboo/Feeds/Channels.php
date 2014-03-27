<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Channel;

class Channels {

    private $_feed = 'channels';
    private $_response;

    public function __construct($params = array()) {
 	    $this->_response = Client::getInstance()->request(
 	    	$this->_feed, $params
 	    );
    }

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