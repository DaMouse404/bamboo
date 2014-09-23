<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Broadcast;

class Broadcasts extends BaseParallel
{

    protected $_feedName = '/channels/{channel_id}/broadcasts';
    protected $_feeds = array();
    protected $_responses;

    public function __construct($params, $channels) {
        $this->_feeds = $this->_buildFeeds($channels);
        parent::__construct($params);
    }

    public function getBroadcasts() {
        $channels = array();
        foreach ($this->_responses as $resp) {
            $id = $resp->broadcasts->channel->id;
            $channels[$id] = $this->_buildModels($resp->broadcasts->elements);
        }

        return $channels;
    }

    private function _buildFeeds($channels) {
        $feedName = $this->_feedName;
        if (!is_array($channels)) {
            $channels = array($channels);
        }
        return array_map(
            function ($channel) use ($feedName) {
                return str_replace("{channel_id}", $channel, $feedName);
            },
            $channels
        );
    }
}
