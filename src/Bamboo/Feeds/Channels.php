<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Channel;

class Channels extends Base
{

    protected $_feed = 'channels';
    protected $_response;

    public function __construct($params = array(), $partner = false) {
        if ($partner) {
            $this->_feed .= '/partner';
        }
        parent::__construct($params);
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
