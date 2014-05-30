<?php

namespace Bamboo\Feeds;

class StaticBase extends Base
{
    public function __construct() {
        $this->_response = $this->fetchAssetFeed($this->_feed);
    }
}
