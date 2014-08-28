<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Broadcast;

class Broadcasts extends Base
{

    protected $_feed = '/channels/{channel_id}/broadcasts';
    protected $_response;

    public function __construct($params, $channel) {
        $this->_setChannel($channel);
        parent::__construct($params);
    }

    private function _setChannel($channel) {
        $this->_feed = str_replace("{channel_id}", $channel, $this->_feed);
    }

    public function getBroadcasts() {
        $broadcasts = array();
        foreach ($this->_response->broadcasts->elements as $broadcast) {
            $broadcasts[] = new Broadcast($broadcast);
        }
        return $broadcasts;
    }
}
