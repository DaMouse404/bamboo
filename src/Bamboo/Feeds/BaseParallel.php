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

        // If there's only one request, just make it normally
        // Otherwise, request them all together
        // Bundle responses together as if they were all one response
        if (count($requests) == 1) {
            $this->_response = Client::getInstance()->request($requests[0][0], $requests[0][1]);
        } else {
            $responses = Client::getInstance()->requestAll($requests);
            $baseResponse = array_shift($responses);
            foreach ($responses as $response) {
                $baseResponse->programmes = array_merge($baseResponse->programmes, $response->programmes);
            }

            $this->_response = $baseResponse;
        }
    }
}
