<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds\Highlights\Base;
use Bamboo\Models;

class Channel extends Base
{

    protected $_feed = 'channels/{id}/highlights';
    protected $_response;

    public function getElements() {
        return $this->_buildModels($this->_response->channel_highlights->elements);
    }

    public function getChannel() {
        $channel = $this->_response->channel_highlights->channel;

        return new Models\Channel($channel);
    }

}
