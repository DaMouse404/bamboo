<?php

namespace Bamboo\Feeds;

use Bamboo\Client;

class BaseParallel extends Base
{

    public function __construct($params = array()) {
        // Build up an array of requests to make
        $requests = array_map(
            function ($feed) use ($params) {
                return array($feed, $params);
            },
            $this->_feeds
        );

        $this->_responses = Client::getInstance()->requestAll($requests);
    }
}
